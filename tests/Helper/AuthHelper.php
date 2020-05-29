<?php

namespace Tests\Helper;

use App\Auth\JWTManager;
use App\Users\UserModel;
use Illuminate\Http\JsonResponse;
use Mockery as m;
use Mockery\MockInterface;
use SPie\LaravelJWT\Contracts\JWT;
use SPie\LaravelJWT\Contracts\JWTAuthenticatable;
use SPie\LaravelJWT\Contracts\JWTGuard;
use SPie\LaravelJWT\Contracts\JWTHandler;

/**
 * Trait AuthHelper
 *
 * @package Tests\Helper
 */
trait AuthHelper
{
    /**
     * @return JWT|MockInterface
     */
    private function createJWT(): JWT
    {
        return m::spy(JWT::class);
    }

    /**
     * @return JWTGuard|MockInterface
     */
    private function createJWTGuard(): JWTGuard
    {
        return m::spy(JWTGuard::class);
    }

    /**
     * @param JWTGuard|MockInterface $jwtGuard
     * @param JWTAuthenticatable     $authenticatable
     *
     * @return $this
     */
    private function assertJWTGuardIssueAccessToken(MockInterface $jwtGuard, JWTAuthenticatable $authenticatable): self
    {
        $jwtGuard
            ->shouldHaveReceived('issueAccessToken')
            ->with($authenticatable)
            ->once();

        return $this;
    }

    /**
     * @param JWTGuard|MockInterface $jwtGuard
     *
     * @return $this
     */
    private function assertJWTGuardIssueRefreshToken(MockInterface $jwtGuard): self
    {
        $jwtGuard
            ->shouldHaveReceived('issueRefreshToken')
            ->once();

        return $this;
    }

    /**
     * @param JWTGuard|MockInterface $jwtGuard
     * @param JsonResponse           $response
     * @param JsonResponse           $inputResponse
     *
     * @return $this
     */
    private function mockJWTGuardReturnAccessToken(
        MockInterface $jwtGuard,
        JsonResponse $response,
        JsonResponse $inputResponse
    ): self {
        $jwtGuard
            ->shouldReceive('returnAccessToken')
            ->with($inputResponse)
            ->andReturn($response);

        return $this;
    }

    /**
     * @param JWTGuard|MockInterface $jwtGuard
     * @param JsonResponse           $response
     * @param JsonResponse           $inputResponse
     *
     * @return $this
     */
    private function mockJWTGuardReturnRefreshToken(
        MockInterface $jwtGuard,
        JsonResponse $response,
        JsonResponse $inputResponse
    ): self {
        $jwtGuard
            ->shouldReceive('returnRefreshToken')
            ->with($inputResponse)
            ->andReturn($response);

        return $this;
    }

    /**
     * @return JWTHandler|MockInterface
     */
    private function createJWTHandler(): JWTHandler
    {
        return m::spy(JWTHandler::class);
    }

    /**
     * @return JWTManager|MockInterface
     */
    private function createJWTManager(): JWTManager
    {
        return m::spy(JWTManager::class);
    }

    /**
     * @param JWTManager|MockInterface $jwtManager
     * @param JsonResponse                 $response
     * @param UserModel                $user
     * @param JsonResponse                 $inputResponse
     * @param bool                     $withRefreshToken
     *
     * @return $this
     */
    private function mockJWTManagerIssueTokens(
        MockInterface $jwtManager,
        JsonResponse $response,
        UserModel $user,
        JsonResponse $inputResponse,
        bool $withRefreshToken
    ): self {
        $jwtManager
            ->shouldReceive('issueTokens')
            ->with($user, $inputResponse, $withRefreshToken)
            ->andReturn($response);

        return $this;
    }
}
