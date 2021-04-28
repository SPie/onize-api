<?php

namespace App\Providers;

use App\Auth\UserProvider;
use App\Policies\ProjectPolicy;
use App\Policies\RolePolicy;
use App\Projects\ProjectModel;
use App\Projects\RoleModel;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Class AuthServiceProvider
 *
 * @package App\Providers
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        ProjectModel::class => ProjectPolicy::class,
        RoleModel::class    => RolePolicy::class,
    ];

    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(StatefulGuard::class, fn () => $this->app->get('auth')->guard());
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        $this->app->get('auth')->provider('app_user_provider', function ($app, array $config) {
            return $this->app->get(UserProvider::class);
        });
    }
}
