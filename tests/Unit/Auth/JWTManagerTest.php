<?php

namespace Tests\Unit\Auth;

use App\Auth\JWTManager;
use SPie\LaravelJWT\Contracts\JWTGuard;
use SPie\LaravelJWT\Contracts\JWTHandler;
use Tests\Helper\AuthHelper;
use Tests\Helper\HttpHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class JWTManagerTest
 *
 * @package Tests\Unit\Auth
 */
final class JWTManagerTest extends TestCase
{
    use AuthHelper;
    use HttpHelper;
    use UsersHelper;

    //region Tests

    private function setUpIssueTokenTest(bool $withRefreshToken = false): array
    {
        $user = $this->createUserModel();
        $inputResponse = $this->createJsonResponse();
        $refreshTokenResponse = $this->createJsonResponse();
        $response = $this->createJsonResponse();
        $jwtGuard = $this->createJWTGuard();
        $this
            ->mockJWTGuardReturnRefreshToken($jwtGuard, $refreshTokenResponse, $inputResponse)
            ->mockJWTGuardReturnAccessToken($jwtGuard, $response, $withRefreshToken ? $refreshTokenResponse : $inputResponse);
        $jwtManager = $this->getJWTManager($jwtGuard);

        return [$jwtManager, $user, $inputResponse, $response, $jwtGuard];
    }

    /**
     * @return void
     */
    public function testIssueToken(): void
    {
        /**
         * @var JWTManager $jwtManager
         * @var JWTGuard   $jwtGuard
         */
        [$jwtManager, $user, $inputResponse, $response, $jwtGuard] = $this->setUpIssueTokenTest();

        $this->assertEquals($response, $jwtManager->issueTokens($user, $inputResponse));
        $this->assertJWTGuardIssueAccessToken($jwtGuard, $user);
    }

    /**
     * @return void
     */
    public function testIssueTokenWithRefreshToken(): void
    {
        /**
         * @var JWTManager $jwtManager
         * @var JWTGuard   $jwtGuard
         */
        [$jwtManager, $user, $inputResponse, $response, $jwtGuard] = $this->setUpIssueTokenTest(true);

        $this->assertEquals($response, $jwtManager->issueTokens($user, $inputResponse, true));
        $this->assertJWTGuardIssueRefreshToken($jwtGuard);
    }

    //endregion

    /**
     * @param JWTGuard|null   $jwtGuard
     * @param JWTHandler|null $jwtHandler
     *
     * @return JWTManager
     */
    private function getJWTManager(JWTGuard $jwtGuard = null, JWTHandler $jwtHandler = null): JWTManager
    {
        return new JWTManager(
            $jwtGuard ?: $this->createJWTGuard(),
            $jwtHandler ?: $this->createJWTHandler()
        );
    }
}
