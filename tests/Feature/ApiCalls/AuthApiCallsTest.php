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

final class AuthApiCallsTest extends FeatureTestCase
{
    use ApiHelper;
    use DatabaseMigrations;
    use ModelHelper;
    use UsersHelper;

    //region Tests

    private function setUpAuthenticateTest(): array
    {
        $email = $this->getFaker()->safeEmail;
        $password = $this->getFaker()->password;
        $this->createUserEntities(1, ['email' => $email, 'password' => Hash::make($password)])->first();

        return [$email, $password];
    }

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

    public function testAuthenticateAndCheckForAuthTokens(): void
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
        $this->assertNotNull($response->headers->get('x-authorize'));
        $this->assertNotNull($response->headers->get('x-refresh'));
    }

    private function setUpAuthenticatedUserTest(bool $withAuthenticatedUser = true): array
    {
        $user = $this->createUserEntities()->first();
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }

        return [$user];
    }

    public function testAuthenticatedUser(): void
    {
        [$user] = $this->setUpAuthenticatedUserTest();

        $response = $this->doApiCall('GET', URL::route(AuthController::ROUTE_NAME_AUTHENTICATED));

        $response->assertStatus(200);
        $response->assertJsonFragment(['user' => $user->toArray()]);
    }

    public function testAuthenticatedUserWithoutAuthenticatedUser(): void
    {
        $this->setUpAuthenticatedUserTest(false);

        $response = $this->doApiCall('GET', URL::route(AuthController::ROUTE_NAME_AUTHENTICATED));

        $response->assertStatus(401);
    }

    private function setUpLogoutTest(bool $withAuthenticatedUser = true): array
    {
        $user = $this->createUserEntities()->first();
        if ($withAuthenticatedUser) {
            $this->actingAs($user);
        }

        return [];
    }

    public function testLogout(): void
    {
        $this->setUpLogoutTest();

        $response = $this->doApiCall('POST', $this->getUrl(AuthController::ROUTE_NAME_LOGOUT));

        $response->assertNoContent();
        $this->assertGuest();
    }

    public function testLogoutWithoutAuthenticatedUser(): void
    {
        $this->setUpLogoutTest(false);

        $response = $this->doApiCall('POST', $this->getUrl(AuthController::ROUTE_NAME_LOGOUT));

        $response->assertStatus(401);
    }

    public function testLogoutWithoutTokens(): void
    {
        $this->setUpLogoutTest();

        $response = $this->doApiCall('POST', $this->getUrl(AuthController::ROUTE_NAME_LOGOUT));

        $this->assertNull($response->headers->get('x-authorization'));
        $this->assertNull($response->headers->get('x-refresh'));
    }

    //endregion
}
