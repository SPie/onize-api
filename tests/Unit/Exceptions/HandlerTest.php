<?php

namespace Tests\Unit\Exceptions;

use App\Exceptions\Handler;
use Illuminate\Contracts\Container\Container;
use Tests\Helper\AppHelper;
use Tests\TestCase;

/**
 * Class HandlerTest
 *
 * @package Tests\Unit\Exceptions
 */
final class HandlerTest extends TestCase
{
    use AppHelper;

    //region Tests

    /**
     * @return void
     */
    public function testRenderForModelNotFoundException(): void
    {
        // TODO
    }

    //endregion

    /**
     * @param Container|null $container
     *
     * @return Handler
     */
    private function getHandler(Container $container = null): Handler
    {
        return new Handler($container ?: $this->createContainer());
    }
}
