<?php

namespace Tests\Helper;

use Illuminate\Contracts\Container\Container;
use Mockery as m;
use Mockery\MockInterface;

/**
 * Trait AppHelper
 *
 * @package Tests\Helper
 */
trait AppHelper
{
    /**
     * @return Container|MockInterface
     */
    private function createContainer(): Container
    {
        return m::spy(Container::class);
    }
}
