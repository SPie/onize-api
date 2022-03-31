<?php

namespace Tests\Unit\Auth;

use App\Auth\RefreshTokenDoctrineModel;
use Carbon\CarbonImmutable;
use Tests\TestCase;

final class RefreshTokenDoctrineModelTest extends TestCase
{
    private function getRefreshTokenDoctrineModel(string $refreshTokenId = null): RefreshTokenDoctrineModel
    {
        return new RefreshTokenDoctrineModel($refreshTokenId ?: $this->getFaker()->word);
    }

    public function testIsRevokedWithoutRevokedDate(): void
    {
        $now = new CarbonImmutable();
        $this->setCarbonMock($now);

        $this->assertFalse($this->getRefreshTokenDoctrineModel()->isRevoked());
    }

    public function testIsRevokedWithRevokedDateInFuture(): void
    {
        $now = new CarbonImmutable();
        $this->setCarbonMock($now);

        $this->assertFalse(
            $this->getRefreshTokenDoctrineModel()
                ->setRevokedAt($now->addDay())
                ->isRevoked()
        );
    }

    public function testIsRevokedWithRevokedToken(): void
    {
        $now = new CarbonImmutable();
        $this->setCarbonMock($now);

        $this->assertTrue(
            $this->getRefreshTokenDoctrineModel()
                ->setRevokedAt($now->subDay())
                ->isRevoked()
        );
    }
}
