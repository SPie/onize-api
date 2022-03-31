<?php

namespace Tests\Unit\Auth;

use App\Auth\AuthManager;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\StatefulGuard;
use Tests\Helper\AuthHelper;
use Tests\Helper\HttpHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class AuthManagerTest extends TestCase
{
    use AuthHelper;
    use HttpHelper;
    use UsersHelper;

    private function getAuthManager(StatefulGuard $guard = null): AuthManager
    {
        return new AuthManager($guard ?: $this->createStatefulGuard());
    }

    private function setUpLoginTest(): array
    {
        $user = $this->createUserModel();
        $guard = $this->createStatefulGuard();
        $jwtManager = $this->getAuthManager($guard);

        return [$jwtManager, $user, $guard];
    }

    public function testLogin(): void
    {
        /**
         * @var AuthManager   $jwtManager
         * @var StatefulGuard $guard
         */
        [$jwtManager, $user, $guard] = $this->setUpLoginTest();

        $this->assertEquals($jwtManager, $jwtManager->login($user));
        $this->assertStatefulGuardLogin($guard, $user);
    }

    private function setUpAuthenticatedUserTest(bool $withAuthenticatedUser = true): array
    {
        $user = $this->createUserModel();
        $guard = $this->createStatefulGuard();
        $this->mockStatefulGuardUser($guard, $withAuthenticatedUser ? $user : null);
        $authManager = $this->getAuthManager($guard);

        return [$authManager, $user];
    }

    public function testAuthenticatedUser(): void
    {
        /** @var AuthManager $authManager */
        [$authManager, $user] = $this->setUpAuthenticatedUserTest();

        $this->assertEquals($user, $authManager->authenticatedUser());
    }

    public function testAuthenticatedUserWithoutAuthenticatedUser(): void
    {
        /** @var AuthManager $authManager */
        [$authManager] = $this->setUpAuthenticatedUserTest(false);

        $this->expectException(AuthenticationException::class);

        $authManager->authenticatedUser();
    }

    private function setUpAuthenticateTest(bool $authenticated = true): array
    {
        $email = $this->getFaker()->safeEmail;
        $password = $this->getFaker()->password;
        $user = $this->createUserModel();
        $guard = $this->createStatefulGuard();
        $this
            ->mockStatefulGuardAttempt($guard, $authenticated, ['email' => $email, 'password' => $password], true)
            ->mockStatefulGuardUser($guard, $user);
        $authManager = $this->getAuthManager($guard);

        return [$authManager, $email, $password, $user];
    }

    public function testAuthenticate(): void
    {
        /** @var AuthManager $authManager */
        [$authManager, $email, $password, $user] = $this->setUpAuthenticateTest();

        $this->assertEquals($user, $authManager->authenticate($email, $password));
    }

    public function testAuthenticateWithInvalidAttempt(): void
    {
        /** @var AuthManager $authManager */
        [$authManager, $email, $password] = $this->setUpAuthenticateTest(false);

        $this->expectException(AuthorizationException::class);

        $authManager->authenticate($email, $password);
    }

    public function testLogout(): void
    {
        $guard = $this->createStatefulGuard();

        $this->getAuthManager($guard)->logout();

        $guard->shouldHaveReceived('logout')->once();
    }
}
