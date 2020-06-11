<?php

namespace Tests\Helper;

use App\Auth\JWTManager;
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
     * @param JWTGuard|MockInterface $jwtGuard
     * @param UserModel              $user
     *
     * @return $this
     */
    private function assertJWTGuardSetUser(MockInterface $jwtGuard, UserModel $user): self
    {
        $jwtGuard
            ->shouldHaveReceived('setUser')
            ->with($user)
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
