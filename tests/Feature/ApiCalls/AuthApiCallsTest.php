<?php

namespace Tests\Feature\ApiCalls;

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use LaravelDoctrine\Migrations\Testing\DatabaseMigrations;
use Tests\Feature\FeatureTestCase;
use Tests\Helper\ApiHelper;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;

/**
 * Class AuthApiCallsTest
 *
 * @package Tests\Feature\ApiCalls
 */
final class AuthApiCallsTest extends FeatureTestCase
{
    use ApiHelper;
    use DatabaseMigrations;
    use ModelHelper;
    use UsersHelper;

    //region Tests

    /**
     * @return array
     */
    private function setUpAuthenticateTest(): array
    {
        $email = $this->getFaker()->safeEmail;
        $password = $this->getFaker()->password;
        $user = $this->createUserEntities(1, ['email' => $email, 'password' => Hash::make($password)])->first();

        return [$email, $password];
    }

    /**
     * @return void
     */
    public function testAuthenticate(): void
    {
        [$email, $password] = $this->setUpAuthenticateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(AuthController::ROUTE_NAME_AUTHENTICATE),
            [
                'email'    => $email,
                'password' => $password,
            ]
        );

        $response->assertStatus(204);
        $this->isAuthenticated();
    }

    /**
     * @return void
     */
    public function testAuthenticateWithoutRequiredParameter(): void
    {
        $this->setUpAuthenticateTest();

        $response = $this->doApiCall('POST', $this->getUrl(AuthController::ROUTE_NAME_AUTHENTICATE));

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'email'    => ['validation.required'],
            'password' => ['validation.required'],
        ]);
        $this->assertGuest();
    }

    /**
     * @return void
     */
    public function testAuthenticateWithoutFoundEmail(): void
    {
        [$email, $password] = $this->setUpAuthenticateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(AuthController::ROUTE_NAME_AUTHENTICATE),
            [
                'email'    => $this->getFaker()->word . $email,
                'password' => $password,
            ]
        );

        $response->assertStatus(403);
        $this->assertGuest();
    }

    /**
     * @return void
     */
    public function testAuthenticateWithInvalidPassword(): void
    {
        [$email, $password] = $this->setUpAuthenticateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(AuthController::ROUTE_NAME_AUTHENTICATE),
            [
                'email'    => $email,
                'password' => $this->getFaker()->word . $password,
            ]
        );

        $response->assertStatus(403);
        $this->assertGuest();
    }

    /**
     * @param bool $withAuthenticatedUser
     *
     * @return array
     */
    private function setUpAuthenticatedUserTest(bool $withAuthenticatedUser = true): array
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
    public function testAuthenticatedUser(): void
    {
        [$user] = $this->setUpAuthenticatedUserTest();

        $response = $this->doApiCall('GET', URL::route(AuthController::ROUTE_NAME_AUTHENTICATED));

        $response->assertStatus(200);
        $response->assertJsonFragment(['user' => $user->toArray()]);
    }

    /**
     * @return void
     */
    public function testAuthenticatedUserWithoutAuthenticatedUser(): void
    {
        $this->setUpAuthenticatedUserTest(false);

        $response = $this->doApiCall('GET', URL::route(AuthController::ROUTE_NAME_AUTHENTICATED));

        $response->assertStatus(401);
    }

    //endregion
}
