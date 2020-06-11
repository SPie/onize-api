<?php

namespace Tests\Feature\ApiCalls;

use App\Http\Controllers\UsersController;
use App\Users\UserModel;
use App\Users\UserRepository;
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
        $response->assertHeader('Authorization');
        $response->assertCookie('refresh-token');
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
            'email' => ['validation.user-not-unique'],
        ]);
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
