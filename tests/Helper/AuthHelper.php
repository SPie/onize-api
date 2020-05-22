<?php

namespace Tests\Helper;

use App\Auth\JWTManager;
use Mockery as m;
use Mockery\MockInterface;

/**
 * Trait AuthHelper
 *
 * @package Tests\Helper
 */
trait AuthHelper
{
    /**
     * @return JWTManager|MockInterface
     */
    private function createJWTManager(): JWTManager
    {
        return m::spy(JWTManager::class);
    }
}
