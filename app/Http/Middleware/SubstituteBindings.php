<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Http\Request;

/**
 * Class SubstituteBindings
 *
 * @package App\Http\Middleware
 */
final class SubstituteBindings
{
    /**
     * @var Registrar
     */
    private Registrar $router;

    /**
     * SubstituteBindings constructor.
     *
     * @param Registrar $router
     */
    public function __construct(Registrar $router)
    {
        $this->router = $router;
    }

    /**
     * @return Registrar
     */
    private function getRouter(): Registrar
    {
        return $this->router;
    }

    /**
     * @param Request  $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $this->getRouter()->substituteBindings($request->route());

        return $next($request);
    }
}
