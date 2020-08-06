<?php

namespace Tests\Unit\Auth;

use App\Auth\AuthManager;
use SPie\LaravelJWT\Contracts\JWTGuard;
use SPie\LaravelJWT\Contracts\JWTHandler;
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
        $jwtGuard = $this->createJWTGuard();
        $jwtManager = $this->getAuthManager($jwtGuard);

        return [$jwtManager, $user, $jwtGuard];
    }

    /**
     * @return void
     */
    public function testLoginToken(): void
    {
        /**
         * @var AuthManager $jwtManager
         * @var JWTGuard    $jwtGuard
         */
        [$jwtManager, $user, $jwtGuard] = $this->setUpLoginTest();

        $this->assertEquals($jwtManager, $jwtManager->login($user));
        $this->assertJWTGuardLogin($jwtGuard, $user, true);
    }

    //endregion

    /**
     * @param JWTGuard|null $jwtGuard
     *
     * @return AuthManager
     */
    private function getAuthManager(JWTGuard $jwtGuard = null): AuthManager
    {
        return new AuthManager($jwtGuard ?: $this->createJWTGuard());
    }
}
