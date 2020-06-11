<?php

namespace Tests\Unit\Auth;

use App\Auth\JWTRefreshTokenRepository;
use App\Models\Exceptions\ModelNotFoundException;
use App\Auth\RefreshTokenModel;
use App\Auth\RefreshTokenModelFactory;
use App\Auth\RefreshTokenRepository;
use App\Users\UserRepository;
use Mockery as m;
use Mockery\MockInterface;
use SPie\LaravelJWT\Contracts\JWT;
use Tests\Helper\AuthHelper;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class JWTRefreshTokenRepositoryTest
 *
 * @package Tests\Unit\Auth
 */
final class JWTRefreshTokenRepositoryTest extends TestCase
{
    use AuthHelper;
    use ModelHelper;
    use UsersHelper;

    //region Tests

    private function setUpStoreRefreshTokenTest(bool $withUser = true, bool $withExpiresAt = true): array
    {
        $refreshToken = $this->createJWT();
        $this
            ->mockJWTGetSubject($refreshToken, $this->getFaker()->safeEmail)
            ->mockJWTGetRefreshTokenId($refreshToken, $this->getFaker()->word)
            ->mockJWTGetExpiresAt(
                $refreshToken,
                $withExpiresAt
                    ? new \DateTimeImmutable($this->getFaker()->dateTime->format('Y-m-d H:i:s'))
                    : null
            );
        $user = $this->createUserModel();
        $userRepository = $this->createUserRepository();
        $this->mockUserRepositoryFindOneByEmail($userRepository, $withUser ? $user : null, $refreshToken->getSubject());
        $refreshTokenModel = $this->createRefreshTokenModel();
        $refreshTokenModelFactory = $this->createRefreshTokenModelFactory();
        $this->mockRefreshTokenModelFactoryCreate(
            $refreshTokenModelFactory,
            $refreshTokenModel,
            $refreshToken->getRefreshTokenId(),
            $user,
            $withExpiresAt ? new \DateTime($refreshToken->getExpiresAt()->format('Y-m-d H:i:s')) : null
        );
        $refreshTokenRepository = $this->createRefreshTokenRepository();
        $jwtRefreshTokenRepository = $this->getJWTRefreshTokenRepository($refreshTokenRepository, $refreshTokenModelFactory, $userRepository);

        return [$jwtRefreshTokenRepository, $refreshToken, $refreshTokenRepository, $refreshTokenModel];
    }

    /**
     * @return void
     */
    public function testStoreRefreshToken(): void
    {
        /**
         * @var JWTRefreshTokenRepository $jwtRefreshTokenRepository
         * @var RefreshTokenRepository    $refreshTokenRepository
         * @var RefreshTokenModel         $refreshTokenModel
         */
        [$jwtRefreshTokenRepository, $refreshToken, $refreshTokenRepository, $refreshTokenModel] = $this->setUpStoreRefreshTokenTest();

        $this->assertEquals($jwtRefreshTokenRepository, $jwtRefreshTokenRepository->storeRefreshToken($refreshToken));
        $this->assertRepositorySave($refreshTokenRepository, $refreshTokenModel);
    }

    /**
     * @return void
     */
    public function testStoreRefreshTokenWithoutUser(): void
    {
        /** @var JWTRefreshTokenRepository $jwtRefreshTokenRepository */
        [$jwtRefreshTokenRepository, $refreshToken] = $this->setUpStoreRefreshTokenTest(false);

        $this->expectException(ModelNotFoundException::class);

        $jwtRefreshTokenRepository->storeRefreshToken($refreshToken);
    }

    /**
     * @return void
     */
    public function testStoreRefreshTokenWithoutRefreshExpiresAt(): void
    {
        /**
         * @var JWTRefreshTokenRepository $jwtRefreshTokenRepository
         * @var RefreshTokenRepository    $refreshTokenRepository
         * @var RefreshTokenModel         $refreshTokenModel
         */
        [$jwtRefreshTokenRepository, $refreshToken, $refreshTokenRepository, $refreshTokenModel] = $this->setUpStoreRefreshTokenTest(true, false);

        $this->assertEquals($jwtRefreshTokenRepository, $jwtRefreshTokenRepository->storeRefreshToken($refreshToken));
        $this->assertRepositorySave($refreshTokenRepository, $refreshTokenModel);
    }

    private function setUpRevokeRefreshTokenTest(bool $withRefreshToken = true): array
    {
        $now = new \DateTime();
        $refreshTokenId = $this->getFaker()->word;
        $refreshToken = $this->createRefreshTokenModel();
        $this->mockRefreshTokenModelSetValidUntil($refreshToken, $now);
        $refreshTokenRepository = $this->createRefreshTokenRepository();
        $this->mockRefreshTokenRepositoryFindOneByRefreshTokenId(
            $refreshTokenRepository,
            $withRefreshToken ? $refreshToken : null,
            $refreshTokenId
        );
        $jwtRefreshTokenRepository = $this->getJWTRefreshTokenRepository($refreshTokenRepository);

        return [$jwtRefreshTokenRepository, $refreshTokenId, $refreshTokenRepository, $refreshToken, $now];
    }

    /**
     * @return void
     */
    public function testRevokeRefreshToken(): void
    {
        /** @var JWTRefreshTokenRepository $jwtRefreshTokenRepository */
        [$jwtRefreshTokenRepository, $refreshTokenId, $refreshTokenRepository, $refreshToken, $now] = $this->setUpRevokeRefreshTokenTest();

        $jwtRefreshTokenRepository->revokeRefreshToken($refreshTokenId);
        $this->assertRefreshTokenModelSetValidUntil($refreshToken, $now);
        $this->assertRepositorySave($refreshTokenRepository, $refreshToken);
    }

    /**
     * @return void
     */
    public function testRevokeRefreshTokenWithoutRefreshToken(): void
    {
        /** @var JWTRefreshTokenRepository $jwtRefreshTokenRepository */
        [$jwtRefreshTokenRepository, $refreshTokenId] = $this->setUpRevokeRefreshTokenTest(false);

        $this->expectException(ModelNotFoundException::class);

        $jwtRefreshTokenRepository->revokeRefreshToken($refreshTokenId);
    }

    /**
     * @param bool $withRevokedRefreshToken
     * @param bool $withRefreshTokenModel
     *
     * @return array
     */
    private function setUpIsRefreshTokenRevokedTest(
        bool $withRevokedRefreshToken = false,
        bool $withRefreshTokenModel = true
    ): array {
        $refreshTokenId = $this->getFaker()->word;
        $validUntil = $withRevokedRefreshToken
            ? (new \DateTime())->sub(new \DateInterval('P1D'))
            : (new \DateTime())->add(new \DateInterval('P1D'));
        $refreshToken = $this->createRefreshTokenModel();
        $this->mockRefreshTokenModelGetValidUntil($refreshToken, $validUntil);
        $refreshTokenRepository = $this->createRefreshTokenRepository();
        $this->mockRefreshTokenRepositoryFindOneByRefreshTokenId(
            $refreshTokenRepository,
            $withRefreshTokenModel ? $refreshToken : null,
            $refreshTokenId
        );
        $jwtRefreshTokenRepository = $this->getJWTRefreshTokenRepository($refreshTokenRepository);

        return [$jwtRefreshTokenRepository, $refreshTokenId];
    }

    /**
     * @return void
     */
    public function testIsRefreshTokenRevokedWithoutRevokedRefreshToken(): void
    {
        /** @var JWTRefreshTokenRepository $jwtRefreshTokenRepository */
        [$jwtRefreshTokenRepository, $refreshTokenId] = $this->setUpIsRefreshTokenRevokedTest();

        $this->assertFalse($jwtRefreshTokenRepository->isRefreshTokenRevoked($refreshTokenId));
    }

    /**
     * @return void
     */
    public function testIsRefreshTokenRevokedWithRevokedRefreshToken(): void
    {
        /** @var JWTRefreshTokenRepository $jwtRefreshTokenRepository */
        [$jwtRefreshTokenRepository, $refreshTokenId] = $this->setUpIsRefreshTokenRevokedTest(true);

        $this->assertTrue($jwtRefreshTokenRepository->isRefreshTokenRevoked($refreshTokenId));
    }

    /**
     * @return void
     */
    public function testIsRefreshTokenRevokedWithoutRefreshToken(): void
    {
        /** @var JWTRefreshTokenRepository $jwtRefreshTokenRepository */
        [$jwtRefreshTokenRepository, $refreshTokenId] = $this->setUpIsRefreshTokenRevokedTest(false, false);

        $this->assertTrue($jwtRefreshTokenRepository->isRefreshTokenRevoked($refreshTokenId));
    }

    //endregion

    /**
     * @param RefreshTokenRepository|null   $refreshTokenRepository
     * @param RefreshTokenModelFactory|null $refreshTokenModelFactory
     * @param UserRepository|null           $userRepository
     *
     * @return JWTRefreshTokenRepository
     */
    private function getJWTRefreshTokenRepository(
        RefreshTokenRepository $refreshTokenRepository = null,
        RefreshTokenModelFactory $refreshTokenModelFactory = null,
        UserRepository $userRepository = null
    ): JWTRefreshTokenRepository {
        return new JWTRefreshTokenRepository(
            $refreshTokenRepository ?: $this->createRefreshTokenRepository(),
            $refreshTokenModelFactory ?: $this->createRefreshTokenModelFactory(),
            $userRepository ?: $this->createUserRepository()
        );
    }
    /**
     * @return JWT|MockInterface
     */
    private function createJWT(): JWT
    {
        return m::spy(JWT::class);
    }

    /**
     * @param JWT|MockInterface $jwt
     * @param string            $subject
     *
     * @return $this
     */
    private function mockJWTGetSubject(MockInterface $jwt, string $subject): self
    {
        $jwt
            ->shouldReceive('getSubject')
            ->andReturn($subject);

        return $this;
    }

    /**
     * @param JWT|MockInterface $jwt
     * @param string            $refreshTokenId
     *
     * @return $this
     */
    private function mockJWTGetRefreshTokenId(MockInterface $jwt, string $refreshTokenId): self
    {
        $jwt
            ->shouldReceive('getRefreshTokenId')
            ->andReturn($refreshTokenId);

        return $this;
    }

    /**
     * @param JWT|MockInterface       $jwt
     * @param \DateTimeImmutable|null $expiresAt
     *
     * @return $this
     */
    private function mockJWTGetExpiresAt(MockInterface $jwt, ?\DateTimeImmutable $expiresAt): self
    {
        $jwt
            ->shouldReceive('getExpiresAt')
            ->andReturn($expiresAt);

        return $this;
    }
}
