<?php

namespace Tests\Unit\Auth;

use App\Auth\RefreshTokenDoctrineModel;
use App\Auth\RefreshTokenDoctrineModelFactory;
use Tests\TestCase;

final class RefreshTokenDoctrineModelFactoryTest extends TestCase
{
    private function getRefreshTokenDoctrineModelFactory(): RefreshTokenDoctrineModelFactory
    {
        return new RefreshTokenDoctrineModelFactory();
    }

    public function testCreate(): void
    {
        $refreshTokenId = $this->getFaker()->word;

        $this->assertEquals(
            new RefreshTokenDoctrineModel($refreshTokenId),
            $this->getRefreshTokenDoctrineModelFactory()->create($refreshTokenId)
        );
    }
}
