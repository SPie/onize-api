<?php

namespace Tests\Unit\Auth;

use App\Auth\RefreshTokenDoctrineModel;
use App\Auth\RefreshTokenDoctrineModelFactory;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class RefreshTokenDoctrineModelFactoryTest
 *
 * @package Tests\Unit\Auth
 */
final class RefreshTokenDoctrineModelFactoryTest extends TestCase
{
    use UsersHelper;

    //region Tests

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $identifier = $this->getFaker()->word;
        $user = $this->createUserModel();
        $validUntil = $this->getFaker()->dateTime;

        $this->assertEquals(
            new RefreshTokenDoctrineModel($identifier, $user, $validUntil),
            $this->getRefreshTokenDoctrineModelFactory()->create($identifier, $user, $validUntil)
        );
    }

    /**
     * @return void
     */
    public function testCreateWithoutValidUntil(): void
    {
        $identifier = $this->getFaker()->word;
        $user = $this->createUserModel();

        $this->assertEquals(
            new RefreshTokenDoctrineModel($identifier, $user, null),
            $this->getRefreshTokenDoctrineModelFactory()->create($identifier, $user)
        );
    }

    //endregion

    private function getRefreshTokenDoctrineModelFactory(): RefreshTokenDoctrineModelFactory
    {
        return new RefreshTokenDoctrineModelFactory();
    }
}
