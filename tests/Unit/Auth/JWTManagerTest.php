<?php

namespace Tests\Unit\Auth;

use App\Auth\JWTManager;
use App\Auth\RefreshTokenModelFactory;
use App\Auth\RefreshTokenRepository;
use Carbon\CarbonImmutable;
use Mockery as m;
use Mockery\MockInterface;
use SPie\LaravelJWT\Contracts\JWT;
use Tests\Helper\AuthHelper;
use Tests\Helper\ModelHelper;
use Tests\TestCase;

final class JWTManagerTest extends TestCase
{
    use AuthHelper;
    use ModelHelper;

    private function getJWTManager(
        RefreshTokenRepository $refreshTokenRepository = null,
        RefreshTokenModelFactory $refreshTokenModelFactory = null
    ): JWTManager {
        return new JWTManager(
            $refreshTokenRepository ?: $this->createRefreshTokenRepository(),
            $refreshTokenModelFactory ?: $this->createRefreshTokenModelFactory()
        );
    }

    /**
     * @return JWT|MockInterface
     */
    private function createJWT(): JWT
    {
        return m::spy(JWT::class);
    }

    private function setUpStoreRefreshTokenTest(): array
    {
        $refreshTokenId = $this->getFaker()->word;
        $jwt = $this->createJWT();
        $jwt
            ->shouldReceive('getRefreshTokenId')
            ->andReturn($refreshTokenId);
        $refreshToken = $this->createRefreshTokenModel();
        $refreshTokenModelFactory = $this->createRefreshTokenModelFactory();
        $this->mockRefreshTokenModelFactoryCreate($refreshTokenModelFactory, $refreshToken, $refreshTokenId);
        $refreshTokenRepository = $this->createRefreshTokenRepository();
        $this->mockRepositorySave($refreshTokenRepository, $refreshToken);
        $jwtManager = $this->getJWTManager($refreshTokenRepository, $refreshTokenModelFactory);

        return [$jwtManager, $jwt, $refreshTokenRepository, $refreshToken];
    }

    public function testStoreRefreshToken(): void
    {
        /** @var JWTManager $jwtManager */
        [$jwtManager, $jwt, $refreshTokenRepository, $refreshToken] = $this->setUpStoreRefreshTokenTest();

        $this->assertEquals($jwtManager, $jwtManager->storeRefreshToken($jwt));
        $this->assertRepositorySave($refreshTokenRepository, $refreshToken);
    }

    private function setUpRevokeRefreshTokenTest(bool $withRefreshToken = true): array
    {
        $now = new CarbonImmutable();
        $this->setCarbonMock($now);
        $refreshTokenId = $this->getFaker()->word;
        $refreshToken = $this->createRefreshTokenModel();
        $refreshToken
            ->shouldReceive('setRevokedAt')
            ->andReturn($refreshToken);
        $refreshTokenRepository = $this->createRefreshTokenRepository();
        $this->mockRefreshTokenRepositoryFindOneByRefreshTokenId($refreshTokenRepository, $withRefreshToken ? $refreshToken : null, $refreshTokenId);
        $jwtManager = $this->getJWTManager($refreshTokenRepository);

        return [$jwtManager, $refreshTokenId, $refreshTokenRepository, $refreshToken];
    }

    public function testRevokeRefreshToken(): void
    {
        /** @var JWTManager $jwtManager */
        [$jwtManager, $refreshTokenId, $refreshTokenRepository, $refreshToken] = $this->setUpRevokeRefreshTokenTest();

        $this->assertEquals($jwtManager, $jwtManager->revokeRefreshToken($refreshTokenId));
        $this->assertRepositorySave($refreshTokenRepository, $refreshToken);
        $refreshToken
            ->shouldHaveReceived('setRevokedAt')
            ->with(m::on(fn (\DateTimeImmutable $actual) => $actual == new CarbonImmutable()))
            ->once();
    }

    public function testRevokeRefreshTokenWithoutRefreshToken(): void
    {
        /** @var JWTManager $jwtManager */
        [$jwtManager, $refreshTokenId, $refreshTokenRepository] = $this->setUpRevokeRefreshTokenTest(false);

        $this->assertEquals($jwtManager, $jwtManager->revokeRefreshToken($refreshTokenId));
        $refreshTokenRepository->shouldNotHaveReceived('save');
    }

    private function setUpIsRefreshTokenRevokedTest(bool $tokenIsRevoked = false, bool $withRefreshToken = true): array
    {
        $refreshTokenId = $this->getFaker()->word;
        $refreshToken = $this->createRefreshTokenModel();
        $this->mockRefreshTokenIsRevoked($refreshToken, $tokenIsRevoked);
        $refreshTokenRepository = $this->createRefreshTokenRepository();
        $this->mockRefreshTokenRepositoryFindOneByRefreshTokenId(
            $refreshTokenRepository,
            $withRefreshToken ? $refreshToken : null,
            $refreshTokenId
        );
        $jwtManager = $this->getJWTManager($refreshTokenRepository);

        return [$jwtManager, $refreshTokenId];
    }

    public function testIsRefreshTokenRevokedWithoutRevokedToken(): void
    {
        /** @var JWTManager $jwtManager */
        [$jwtManager, $refreshTokenId] = $this->setUpIsRefreshTokenRevokedTest();

        $this->assertFalse($jwtManager->isRefreshTokenRevoked($refreshTokenId));
    }

    public function testIsRefreshTokenRevokedWithRevokedToken(): void
    {
        /** @var JWTManager $jwtManager */
        [$jwtManager, $refreshTokenId] = $this->setUpIsRefreshTokenRevokedTest(true);

        $this->assertTrue($jwtManager->isRefreshTokenRevoked($refreshTokenId));
    }

    public function testIsRefreshTokenRevokedWithoutRefreshToken(): void
    {
        /** @var JWTManager $jwtManager */
        [$jwtManager, $refreshTokenId] = $this->setUpIsRefreshTokenRevokedTest(false, false);

        $this->assertTrue($jwtManager->isRefreshTokenRevoked($refreshTokenId));
    }
}
