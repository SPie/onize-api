<?php

namespace Tests\Unit\Auth;

use App\Auth\RefreshTokenDoctrineRepository;
use App\Models\DatabaseHandler;
use Tests\Helper\AuthHelper;
use Tests\Helper\ModelHelper;
use Tests\TestCase;

/**
 * Class RefreshTokenDoctrineRepositoryTest
 *
 * @package Tests\Unit\Auth
 */
final class RefreshTokenDoctrineRepositoryTest extends TestCase
{
    use AuthHelper;
    use ModelHelper;

    //region Tests

    /**
     * @param bool $withRefreshToken
     *
     * @return array
     */
    private function setUpFindOneByRefreshTokenIdTest(bool $withRefreshToken = true): array
    {
        $refreshTokenId = $this->getFaker()->word;
        $refreshToken = $this->createRefreshTokenModel();
        $databaseHandler = $this->createDatabaseHandler();
        $this->mockDatabaseHandlerLoad($databaseHandler, $withRefreshToken ? $refreshToken : null, ['identifier' => $refreshTokenId]);
        $refreshTokenRepository = $this->getRefreshTokenDoctrineRepository($databaseHandler);

        return [$refreshTokenRepository, $refreshTokenId, $refreshToken];
    }

    /**
     * @return void
     */
    public function testFindOneByRefreshTokenId(): void
    {
        /** @var RefreshTokenDoctrineRepository $refreshTokenRepository */
        [$refreshTokenRepository, $refreshTokenId, $refreshToken] = $this->setUpFindOneByRefreshTokenIdTest();

        $this->assertEquals($refreshToken, $refreshTokenRepository->findOneByRefreshTokenId($refreshTokenId));
    }

    /**
     * @return void
     */
    public function testFindOneByRefreshTokenIdWithoutRefreshToken(): void
    {
        /** @var RefreshTokenDoctrineRepository $refreshTokenRepository */
        [$refreshTokenRepository, $refreshTokenId] = $this->setUpFindOneByRefreshTokenIdTest(false);

        $this->assertNull($refreshTokenRepository->findOneByRefreshTokenId($refreshTokenId));
    }

    //endregion

    /**
     * @param DatabaseHandler|null $databaseHandler
     *
     * @return RefreshTokenDoctrineRepository
     */
    private function getRefreshTokenDoctrineRepository(
        DatabaseHandler $databaseHandler = null
    ): RefreshTokenDoctrineRepository {
        return new RefreshTokenDoctrineRepository($databaseHandler ?: $this->createDatabaseHandler());
    }
}
