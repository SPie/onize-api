<?php

namespace Tests\Feature\ApiCalls;

use App\Http\Controllers\UsersController;
use App\Users\UserModel;
use App\Users\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use LaravelDoctrine\Migrations\Testing\DatabaseMigrations;
use Tests\Feature\FeatureTestCase;
use Tests\Helper\ApiHelper;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;

/**
 * Class UsersApiCallsTest
 *
 * @package Tests\Feature\ApiCalls
 */
final class UsersApiCallsTest extends FeatureTestCase
{
    use ApiHelper;
    use DatabaseMigrations;
    use ModelHelper;
    use UsersHelper;

    //region Tests

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testRegisterWithoutRequiredParameters(): void
    {
        $response = $this->doApiCall('POST', URL::route(UsersController::ROUTE_NAME_REGISTER));

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'email'    => ['validation.required'],
            'password' => ['validation.required'],
        ]);
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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
            'email' => ['validation.user_not_unique'],
        ]);
    }

    /**
     * @return array
     */
    private function setUpUpdateTest(bool $withAuthenticatedUser = true): array
    {
        $user = $this->createUserEntities()->first();
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }

        return [$user, $this->createUserEntities()->first()->getEmail()];
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testUpdateWithExistingEmail(): void
    {
        [$user, $existingEmail] = $this->setUpUpdateTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE),
            ['email' => $existingEmail]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['email' => ['validation.user_not_unique']]);
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @param bool $withAuthenticatedUser
     *
     * @return array
     */
    private function setUpUpdatePasswordTest(bool $withAuthenticatedUser = true): array
    {
        $user = $this->createUserEntities()->first();
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }

        return [$user];
    }

    /**
     * @return void
     */
    public function testUpdatePassword(): void
    {
        [$user] = $this->setUpUpdatePasswordTest();
        $password = $this->getFaker()->password;

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE_PASSWORD),
            ['password' => $password]
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

    /**
     * @return void
     */
    public function testUpdatePasswordWithoutChange(): void
    {
        [$user] = $this->setUpUpdatePasswordTest();
        $password = $user->getPassword();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE_PASSWORD),
        );

        $response->assertOk();
        $response->assertJsonFragment([
            'user' => [
                'uuid'  => $user->getUuid(),
                'email' => $user->getEmail(),
            ]
        ]);
        $this->assertEquals($password, $user->getPassword());
    }

    /**
     * @return void
     */
    public function testUpdatePasswordWithEmptyPassword(): void
    {
        $this->setUpUpdatePasswordTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE_PASSWORD),
            ['password' => '']
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['password' => ['validation.string']]);
    }

    /**
     * @return void
     */
    public function testUpdatePasswordWithInvalidPassword(): void
    {
        $this->setUpUpdatePasswordTest();

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE_PASSWORD),
            ['password' => $this->getFaker()->numberBetween()]
        );

        $response->assertStatus(422);
        $response->assertJsonFragment(['password' => ['validation.string']]);
    }

    /**
     * @return void
     */
    public function testUpdatePasswordWithoutAuthenticatedUser(): void
    {
        $this->setUpUpdatePasswordTest(false);

        $response = $this->doApiCall(
            'PATCH',
            $this->getUrl(UsersController::ROUTE_NAME_UPDATE_PASSWORD),
            ['password' => $this->getFaker()->password]
        );

        $response->assertStatus(401);
    }

    //endregion

    /**
     * @return UserRepository
     */
    private function getConcreteUserRepository(): UserRepository
    {
        return $this->app->get(UserRepository::class);
    }
}
