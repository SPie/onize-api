<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Http\Request;

final class SubstituteBindings
{
    public function __construct(readonly private Registrar $router)
    {
    }

    /**
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $this->router->substituteBindings($request->route());

        return $next($request);
    }
}
