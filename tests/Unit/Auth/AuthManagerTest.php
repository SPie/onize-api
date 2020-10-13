<?php

namespace Tests\Unit\Auth;

use App\Auth\AuthManager;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\StatefulGuard;
use Tests\Helper\AuthHelper;
use Tests\Helper\HttpHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class AuthManagerTest
 *
 * @package Tests\Unit\Auth
 */
final class AuthManagerTest extends TestCase
{
    use AuthHelper;
    use HttpHelper;
    use UsersHelper;

    //region Tests

    private function setUpLoginTest(): array
    {
        $user = $this->createUserModel();
        $guard = $this->createStatefulGuard();
        $jwtManager = $this->getAuthManager($guard);

        return [$jwtManager, $user, $guard];
    }

    /**
     * @return void
     */
    public function testLoginToken(): void
    {
        /**
         * @var AuthManager   $jwtManager
         * @var StatefulGuard $guard
         */
        [$jwtManager, $user, $guard] = $this->setUpLoginTest();

        $this->assertEquals($jwtManager, $jwtManager->login($user));
        $this->assertStatefulGuardLogin($guard, $user, true);
    }

    /**
     * @param bool $withAuthenticatedUser
     *
     * @return array
     */
    private function setUpAuthenticatedUserTest(bool $withAuthenticatedUser = true): array
    {
        $user = $this->createUserModel();
        $guard = $this->createStatefulGuard();
        $this->mockStatefulGuardUser($guard, $withAuthenticatedUser ? $user : null);
        $authManager = $this->getAuthManager($guard);

        return [$authManager, $user];
    }

    /**
     * @return void
     */
    public function testAuthenticatedUser(): void
    {
        /** @var AuthManager $authManager */
        [$authManager, $user] = $this->setUpAuthenticatedUserTest();

        $this->assertEquals($user, $authManager->authenticatedUser());
    }

    /**
     * @return void
     */
    public function testAuthenticatedUserWithoutAuthenticatedUser(): void
    {
        /** @var AuthManager $authManager */
        [$authManager] = $this->setUpAuthenticatedUserTest(false);

        $this->expectException(AuthenticationException::class);

        $authManager->authenticatedUser();
    }

    //endregion

    /**
     * @param StatefulGuard|null $guard
     *
     * @return AuthManager
     */
    private function getAuthManager(StatefulGuard $guard = null): AuthManager
    {
        return new AuthManager($guard ?: $this->createStatefulGuard());
    }
}
