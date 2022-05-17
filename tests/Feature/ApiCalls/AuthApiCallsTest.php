<?php

namespace Tests\Feature\ApiCalls;

use App\Auth\RefreshTokenModel;
use App\Auth\RefreshTokenRepository;
use App\Http\Controllers\AuthController;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use LaravelDoctrine\Migrations\Testing\DatabaseMigrations;
use SPie\LaravelJWT\Contracts\TokenBlockList;
use Tests\Feature\FeatureTestCase;
use Tests\Helper\ApiHelper;
use Tests\Helper\ModelHelper;
use Tests\Helper\ReflectionHelper;
use Tests\Helper\UsersHelper;

final class AuthApiCallsTest extends FeatureTestCase
{
    use ApiHelper;
    use DatabaseMigrations;
    use ModelHelper;
    use ReflectionHelper;
    use UsersHelper;

    private function setUpAuthenticateTest(): array
    {
        $email = $this->getFaker()->safeEmail;
        $password = $this->getFaker()->password;
        $user = $this->createUserEntities(1, ['email' => $email, 'password' => Hash::make($password)])->first();

        return [$email, $password, $user];
    }

    public function testAuthenticate(): void
    {
        [$email, $password, $user] = $this->setUpAuthenticateTest();

        $response = $this->doApiCall(
            'POST',
            $this->getUrl(AuthController::ROUTE_NAME_AUTHENTICATE),
            [
                'email'    => $email,
                'password' => $password,
            ]
        );

        $response->assertStatus(200);
        $response->assertJson(['user' => $user->toArray()]);
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

        $response->assertStatus(200);
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

        $this->assertNull($response->headers->get('x-authorize'));
        $this->assertNull($response->headers->get('x-refresh'));
    }

    private function setUpAuthorizationTokensTest(
        bool $withRevokedRefreshToken = false,
        bool $withRevokedAuthorizeToken = false
    ): array {
        $user = $this->createUserEntities()->first();

        $guard = $this->app['auth']->guard();

        $authorizeToken = $this->runPrivateMethod($guard, 'issueAccessToken', [$user]);
        $refreshToken = $this->runPrivateMethod($guard, 'issueRefreshToken', [$user]);
        $this->app['auth']->forgetGuards();

        if ($withRevokedAuthorizeToken) {
            $this->app->get(TokenBlockList::class)->revoke($authorizeToken);
        }
        if ($withRevokedRefreshToken) {
            $refreshTokenRepository = $this->app->get(RefreshTokenRepository::class);
            $refreshTokenModel = $refreshTokenRepository->findOneBy([RefreshTokenModel::PROPERTY_REFRESH_TOKEN_ID => $refreshToken->getRefreshTokenId()]);
            $refreshTokenModel->setRevokedAt((new CarbonImmutable())->subDay());
            $refreshTokenRepository->save($refreshTokenModel);
        }

        return [$authorizeToken, $refreshToken];
    }

    public function testAuthorizationToken(): void
    {
        [$authorizeToken] = $this->setUpAuthorizationTokensTest();

        $response = $this->doApiCall(
            'GET',
            $this->getUrl(AuthController::ROUTE_NAME_AUTHENTICATED),
            [],
            [
                'x-authorize' => \sprintf('Bearer %s', $authorizeToken->getJWT()),
            ]
        );

        $response->assertOk();
    }

    public function testRefreshToken(): void
    {
        [$authorizeToken, $refreshToken] = $this->setUpAuthorizationTokensTest();

        $response = $this->doApiCall(
            'GET',
            $this->getUrl(AuthController::ROUTE_NAME_AUTHENTICATED),
            [],
            [
                'x-authorize' => \sprintf('Bearer %s', $this->getFaker()->word),
                'x-refresh'   => \sprintf('Bearer %s', $refreshToken->getJWT()),
            ]
        );

        $response->assertOk();
    }

    public function testAuthorizationTokenWithInvalidTokenAndNoRefresh(): void
    {
        $response = $this->doApiCall(
            'GET',
            $this->getUrl(AuthController::ROUTE_NAME_AUTHENTICATED),
            [],
            [
                'x-authorize' => \sprintf('Bearer %s', $this->getFaker()->word),
            ]
        );

        $response->assertStatus(401);
    }

    public function testRefreshTokenWithInvalidToken(): void
    {
        $response = $this->doApiCall(
            'GET',
            $this->getUrl(AuthController::ROUTE_NAME_AUTHENTICATED),
            [],
            [
                'x-authorize' => \sprintf('Bearer %s', $this->getFaker()->word),
                'x-refresh'   => \sprintf('Bearer %s', $this->getFaker()->word),
            ]
        );

        $response->assertStatus(401);
    }

    public function testRefreshTokenWithRevokedToken(): void
    {
        [$authorizeToken, $refreshToken] = $this->setUpAuthorizationTokensTest(withRevokedRefreshToken: true);

        $response = $this->doApiCall(
            'GET',
            $this->getUrl(AuthController::ROUTE_NAME_AUTHENTICATED),
            [],
            [
                'x-authorize' => \sprintf('Bearer %s', $this->getFaker()->word),
                'x-refresh'   => \sprintf('Bearer %s', $refreshToken->getJWT()),
            ]
        );

        $response->assertStatus(401);
    }

    public function testAuthorizationTokenWithRevokedToken(): void
    {
        [$authorizeToken] = $this->setUpAuthorizationTokensTest(withRevokedAuthorizeToken: true);

        $response = $this->doApiCall(
            'GET',
            $this->getUrl(AuthController::ROUTE_NAME_AUTHENTICATED),
            [],
            [
                'x-authorize' => \sprintf('Bearer %s', $authorizeToken->getJWT()),
            ]
        );

        $response->assertStatus(401);
    }
}
