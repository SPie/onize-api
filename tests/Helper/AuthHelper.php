<?php

namespace Tests\Helper;

use App\Auth\AuthManager;
use App\Auth\RefreshTokenModel;
use App\Auth\RefreshTokenModelFactory;
use App\Auth\RefreshTokenRepository;
use App\Users\UserModel;
use Illuminate\Http\JsonResponse;
use Mockery as m;
use Mockery\MockInterface;
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
     * @return JWTGuard|MockInterface
     */
    private function createJWTGuard(): JWTGuard
    {
        return m::spy(JWTGuard::class);
    }

    /**
     * @param JWTGuard|MockInterface $jwtGuard
     * @param UserModel     $user
     * @param bool          $remember
     *
     * @return $this
     */
    private function assertJWTGuardLogin(MockInterface $jwtGuard, UserModel $user, bool $remember): self
    {
        $jwtGuard
            ->shouldHaveReceived('login')
            ->with($user, $remember)
            ->once();

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
     * @return AuthManager|MockInterface
     */
    private function createAuthManager(): AuthManager
    {
        return m::spy(AuthManager::class);
    }

    /**
     * @param AuthManager|MockInterface $authManager
     * @param UserModel                 $user
     *
     * @return $this
     */
    private function assertAuthManagerLogin(MockInterface $authManager, UserModel $user): self
    {
        $authManager
            ->shouldHaveReceived('login')
            ->with($user)
            ->once();

        return $this;
    }

    /**
     * @param AuthManager|MockInterface $authManager
     * @param JsonResponse              $response
     * @param UserModel                 $user
     * @param JsonResponse              $inputResponse
     * @param bool                      $withRefreshToken
     *
     * @return $this
     */
    private function mockAuthManagerIssueTokens(
        MockInterface $authManager,
        JsonResponse $response,
        UserModel $user,
        JsonResponse $inputResponse,
        bool $withRefreshToken
    ): self {
        $authManager
            ->shouldReceive('issueTokens')
            ->with($user, $inputResponse, $withRefreshToken)
            ->andReturn($response);

        return $this;
    }

    /**
     * @return RefreshTokenModel|MockInterface
     */
    private function createRefreshTokenModel(): RefreshTokenModel
    {
        return m::spy(RefreshTokenModel::class);
    }

    /**
     * @param RefreshTokenModel|MockInterface $refreshTokenModel
     * @param \DateTime|null                  $validUntil
     *
     * @return $this
     */
    private function mockRefreshTokenModelSetValidUntil(MockInterface $refreshTokenModel, ?\DateTime $validUntil): self
    {
        $refreshTokenModel
            ->shouldReceive('setValidUntil')
            ->with(m::on(function (\DateTime $actual) use ($validUntil) {
                return (
                    $actual->sub(new \DateInterval('PT10S')) < $validUntil
                    && $actual->add(new \DateInterval('PT10S')) > $validUntil
                );
            }))
            ->andReturn($refreshTokenModel);

        return $this;
    }

    /**
     * @param RefreshTokenModel|MockInterface $refreshTokenModel
     * @param \DateTime|null                  $validUntil
     *
     * @return $this
     */
    private function assertRefreshTokenModelSetValidUntil(MockInterface $refreshTokenModel, ?\DateTime $validUntil): self
    {
        $refreshTokenModel
            ->shouldHaveReceived('setValidUntil')
            ->with(m::on(function (\DateTime $actual) use ($validUntil) {
                return (
                    $actual->sub(new \DateInterval('PT10S')) < $validUntil
                    && $actual->add(new \DateInterval('PT10S')) > $validUntil
                );
            }))
            ->once();

        return $this;
    }

    /**
     * @param RefreshTokenModel|MockInterface $refreshTokenModel
     * @param \DateTime|null                  $validUntil
     *
     * @return $this
     */
    private function mockRefreshTokenModelGetValidUntil(MockInterface $refreshTokenModel, ?\DateTime $validUntil): self
    {
        $refreshTokenModel
            ->shouldReceive('getValidUntil')
            ->andReturn($validUntil);

        return $this;
    }

    /**
     * @return RefreshTokenModelFactory|MockInterface
     */
    private function createRefreshTokenModelFactory(): RefreshTokenModelFactory
    {
        return m::spy(RefreshTokenModelFactory::class);
    }

    /**
     * @param RefreshTokenModelFactory|MockInterface $refreshTokenModelFactory
     * @param RefreshTokenModel                      $refreshTokenModel
     * @param string                                 $identifier
     * @param UserModel                              $user
     * @param \DateTime|null                         $validUntil
     *
     * @return $this
     */
    private function mockRefreshTokenModelFactoryCreate(
        MockInterface $refreshTokenModelFactory,
        RefreshTokenModel $refreshTokenModel,
        string $identifier,
        UserModel $user,
        ?\DateTime $validUntil
    ): self {
        $refreshTokenModelFactory
            ->shouldReceive('create')
            ->with(
                $identifier,
                $user,
                m::on(function (?\DateTime $actual) use ($validUntil) {
                    return $actual == $validUntil;
                })
            )
            ->andReturn($refreshTokenModel);

        return $this;
    }

    /**
     * @return RefreshTokenRepository|MockInterface
     */
    private function createRefreshTokenRepository(): RefreshTokenRepository
    {
        return m::spy(RefreshTokenRepository::class);
    }

    /**
     * @param RefreshTokenRepository|MockInterface $refreshTokenRepository
     * @param RefreshTokenModel|null               $refreshTokenModel
     * @param string                               $refreshTokenId
     *
     * @return $this
     */
    private function mockRefreshTokenRepositoryFindOneByRefreshTokenId(
        MockInterface $refreshTokenRepository,
        ?RefreshTokenModel $refreshTokenModel,
        string $refreshTokenId
    ): self {
        $refreshTokenRepository
            ->shouldReceive('findOneByRefreshTokenId')
            ->with($refreshTokenId)
            ->andReturn($refreshTokenModel);

        return $this;
    }
}
