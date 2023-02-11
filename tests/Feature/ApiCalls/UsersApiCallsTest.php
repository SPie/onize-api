<?php

namespace Tests\Feature\ApiCalls;

use App\Http\Controllers\UsersController;
use App\Users\UserModel;
use App\Users\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\Feature\FeatureTestCase;
use Tests\Helper\ApiHelper;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;

final class UsersApiCallsTest extends FeatureTestCase
{
    use ApiHelper;
    use ModelHelper;
    use UsersHelper;

    private function getConcreteUserRepository(): UserRepository
    {
        return $this->app->get(UserRepository::class);
    }

    public function testRegister(): void
    {
        $email = $this->getFaker()->safeEmail;
        $password = $this->getFaker()->password;

        $response = $this->doApiCall(
            'POST',
            URL::route(UsersController::ROUTE_NAME_REGISTER),
            [
                'email'    => $email,
                'password' => $password,
            ]
        );

        $response->assertCreated();
        $user = $this->getConcreteUserRepository()->findOneBy(['email' => $email]);
        $this->assertNotEmpty($user);
        $this->assertAuthenticated();
        $response->assertJsonFragment([
            'email' => $email,
        ]);
        $this->assertNotEmpty($response->json('user'));
        $this->assertNotEmpty($response->json('user.uuid'));
    }

    public function testRegisterWithoutRequiredParameters(): void
    {
        $response = $this->doApiCall('POST', URL::route(UsersController::ROUTE_NAME_REGISTER));

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'email'    => ['validation.required'],
            'password' => ['validation.required'],
        ]);
    }

    public function testRegisterWithInvalidParameters(): void
    {
        $response = $this->doApiCall(
            'POST',
            URL::route(UsersController::ROUTE_NAME_REGISTER),
            [
                'email'    => $this->getFaker()->numberBetween(),
                'password' => $this->getFaker()->numberBetween(),
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'email'    => ['validation.email'],
            'password' => ['validation.string'],
        ]);
    }

    public function testRegisterWithUniqueUser(): void
    {
        /** @var UserModel $user */
        $user = $this->createUserEntities()->first();

        $response = $this->doApiCall(
            'POST',
            URL::route(UsersController::ROUTE_NAME_REGISTER),
            [
                'email'    => $user->getEmail(),
                'password' => $this->getFaker()->password,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'email' => ['validation.user-not-unique'],
        ]);
    }

    private function setUpUpdateTest(bool $withAuthenticatedUser = true): array
    {
        $user = $this->createUserEntities()->first();
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }

        return [$user, $this->createUserEntities()->first()->getEmail()];
    }

    public function testUpdate(): void
    {
        [$user] = $this->setUpUpdateTest();
        $newEmail = $this->getFaker()->word . $user->getEmail();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE),
            ['email' => $newEmail]
        );

        $response->assertOk();
        $response->assertJsonFragment([
            'user' => [
                'uuid'  => $user->getUuid(),
                'email' => $newEmail,
            ]
        ]);
    }

    public function testUpdateWithoutChange(): void
    {
        [$user] = $this->setUpUpdateTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE)
        );

        $response->assertOk();
        $response->assertJsonFragment([
            'user' => [
                'uuid'  => $user->getUuid(),
                'email' => $user->getEmail(),
            ]
        ]);
    }

    public function testUpdateWithEmptyEmail(): void
    {
        $this->setUpUpdateTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE),
            ['email' => '']
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['email' => ['validation.email']]);
    }

    public function testUpdateWithInvalidEmail(): void
    {
        $this->setUpUpdateTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE),
            ['email' => $this->getFaker()->word]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['email' => ['validation.email']]);
    }

    public function testUpdateWithExistingEmail(): void
    {
        [$user, $existingEmail] = $this->setUpUpdateTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE),
            ['email' => $existingEmail]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['email' => ['validation.user-not-unique']]);
    }

    public function testUpdateWithExistingEmailFromAuthenticatedUser(): void
    {
        [$user] = $this->setUpUpdateTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE),
            ['email' => $user->getEmail()]
        );

        $response->assertOk();
    }

    public function testUpdateWithoutAuthenticatedUser(): void
    {
        $this->setUpUpdateTest(false);

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE),
            ['email' => $this->getFaker()->safeEmail]
        );

        $response->assertStatus(401);
    }

    private function setUpUpdatePasswordTest(bool $withAuthenticatedUser = true): array
    {
        $currentPassword = $this->getFaker()->password;
        $user = $this->createUserEntities(1, ['password' => Hash::make($currentPassword)])->first();
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }

        return [$user, $currentPassword];
    }

    public function testUpdatePassword(): void
    {
        [$user, $currentPassword] = $this->setUpUpdatePasswordTest();
        $password = $this->getFaker()->password;

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE_PASSWORD),
            [
                'password'        => $password,
                'currentPassword' => $currentPassword,
            ]
        );

        $response->assertOk();
        $response->assertJsonFragment([
            'user' => [
                'uuid'  => $user->getUuid(),
                'email' => $user->getEmail(),
            ]
        ]);
        $this->assertTrue(Hash::check($password, $user->getPassword()));
    }

    public function testUpdatePasswordWithEmptyPassword(): void
    {
        [$user, $currentPassword] = $this->setUpUpdatePasswordTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE_PASSWORD),
            [
                'password'        => '',
                'currentPassword' => $currentPassword,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['password' => ['validation.required']]);
    }

    public function testUpdatePasswordWithInvalidPassword(): void
    {
        [$user, $currentPassword] = $this->setUpUpdatePasswordTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE_PASSWORD),
            [
                'password'        => $this->getFaker()->numberBetween(),
                'currentPassword' => $currentPassword,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['password' => ['validation.string']]);
    }

    public function testUpdatePasswordWithoutAuthenticatedUser(): void
    {
        [$user, $currentPassword] = $this->setUpUpdatePasswordTest(false);

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE_PASSWORD),
            [
                'password'        => $this->getFaker()->password,
                'currentPassword' => $currentPassword,
            ]
        );

        $response->assertStatus(401);
    }

    public function testUpdatePasswordWithoutCurrentPassword(): void
    {
        $this->setUpUpdatePasswordTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE_PASSWORD),
            ['password' => $this->getFaker()->password]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['currentPassword' => ['validation.required']]);
    }

    public function testUpdatePasswordWithInvalidCurrentPassword(): void
    {
        $this->setUpUpdatePasswordTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE_PASSWORD),
            [
                'password'        => $this->getFaker()->password,
                'currentPassword' => $this->getFaker()->numberBetween(),
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['currentPassword' => ['validation.string', 'validation.invalid-password']]);
    }

    public function testUpdatePasswordWithInvalidCredentials(): void
    {
        [$user, $currentPassword] = $this->setUpUpdatePasswordTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE_PASSWORD),
            [
                'password'        => $this->getFaker()->password,
                'currentPassword' => $currentPassword . $this->getFaker()->word,
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['currentPassword' => ['validation.invalid-password']]);
    }

    public function testUpdatePasswordWithEmptyCurrentPassword(): void
    {
        $this->setUpUpdatePasswordTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE_PASSWORD),
            [
                'password'        => $this->getFaker()->password,
                'currentPassword' => '',
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['currentPassword' => ['validation.required']]);
    }

    public function testUpdatePasswordWithoutPassword(): void
    {
        [$user, $currentPassword] = $this->setUpUpdatePasswordTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE_PASSWORD),
            ['currentPassword' => $currentPassword]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['password' => ['validation.required']]);
    }
}
