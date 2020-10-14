<?php

namespace App\Providers;

use App\Users\UserManager;
use Illuminate\Auth\SessionGuard;
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
        // 'App\Model' => 'App\Policies\ModelPolicy',
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
            return $this->app->get(UserManager::class);
        });
    }
}
