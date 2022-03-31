<?php

namespace Tests\Unit\Auth;

use App\Auth\RefreshTokenDoctrineRepository;
use App\Models\DatabaseHandler;
use Tests\Helper\AuthHelper;
use Tests\Helper\ModelHelper;
use Tests\TestCase;

final class RefreshTokenDoctrineRepositoryTest extends TestCase
{
    use AuthHelper;
    use ModelHelper;

    private function getRefreshTokenDoctrineRepository(DatabaseHandler $databaseHandler = null): RefreshTokenDoctrineRepository
    {
        return new RefreshTokenDoctrineRepository($databaseHandler ?: $this->createDatabaseHandler());
    }

    public function testFindOneByRefreshTokenId(): void
    {
        $refreshTokenId = $this->getFaker()->word;
        $refreshToken = $this->createRefreshTokenModel();
        $databaseHandler = $this->createDatabaseHandler();
        $this->mockDatabaseHandlerLoad($databaseHandler, $refreshToken, ['refreshTokenId' => $refreshTokenId]);

        $this->assertEquals(
            $refreshToken,
            $this->getRefreshTokenDoctrineRepository($databaseHandler)->findOneByRefreshTokenId($refreshTokenId)
        );
    }

    public function testFindOneByRefreshTokenIdWithoutRefreshToken(): void
    {
        $refreshTokenId = $this->getFaker()->word;
        $databaseHandler = $this->createDatabaseHandler();
        $this->mockDatabaseHandlerLoad($databaseHandler, null, ['refreshTokenId' => $refreshTokenId]);

        $this->assertNull(
            $this->getRefreshTokenDoctrineRepository($databaseHandler)->findOneByRefreshTokenId($refreshTokenId)
        );
    }
}
