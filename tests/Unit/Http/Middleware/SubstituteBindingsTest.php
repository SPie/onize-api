<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\SubstituteBindings;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Http\Request;
use Tests\Helper\HttpHelper;
use Tests\TestCase;

/**
 * Class SubstituteBindingsTest
 *
 * @package Tests\Unit\Http\Middleware
 */
final class SubstituteBindingsTest extends TestCase
{
    use HttpHelper;

    //region Tests

    /**
     * @return void
     */
    public function testHandle(): void
    {
        $route = $this->createRoute();
        $request = $this->createRequest();
        $this->mockRequestRoute($request, $route);
        $next = fn (Request $handledRequest) => true;
        $router = $this->createRouter();
        $this->mockRouterSubstituteBindings($router, $route);

        $this->assertTrue($this->getSubstituteBindings($router)->handle($request, $next));
    }

    //endregion

    /**
     * @param Registrar|null $router
     *
     * @return SubstituteBindings
     */
    private function getSubstituteBindings(Registrar $router = null): SubstituteBindings
    {
        return new SubstituteBindings($router ?: $this->createRouter());
    }
}
