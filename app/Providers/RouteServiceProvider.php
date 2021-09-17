<?php

namespace App\Providers;

use App\Http\Binders\InvitationBinder;
use App\Http\Binders\ProjectBinder;
use App\Http\Binders\RoleBinder;
use App\Http\Binders\UserBinder;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootBinders();

        parent::boot();
    }

    private function bootBinders(): self
    {
        Route::bind('project', ProjectBinder::class);
        Route::bind('role', RoleBinder::class);
        Route::bind('invitation', InvitationBinder::class);
        Route::bind('user', UserBinder::class);

        return $this;
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
