<?php

namespace App\Providers;

use App\Auth\UserProvider;
use App\Policies\InvitationPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\RolePolicy;
use App\Projects\Invites\InvitationModel;
use App\Projects\ProjectModel;
use App\Projects\RoleModel;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        ProjectModel::class    => ProjectPolicy::class,
        RoleModel::class       => RolePolicy::class,
        InvitationModel::class => InvitationPolicy::class,
    ];

    public function register(): void
    {
        $this->app->bind(StatefulGuard::class, fn () => $this->app->get('auth')->guard());
//        $this->app->bind(UserProviderContract::class, fn () => $this->app->get('auth')->getDefaultUserProvider());
        $this->app->bind(UserProviderContract::class, function () {
            return $this->app->get('auth')->createUserProvider('app');
        });
    }

    public function boot()
    {
        $this->registerPolicies();

        $this->app->get('auth')->provider('app_user_provider', function ($app, array $config) {
            return $this->app->get(UserProvider::class);
        });
    }
}
