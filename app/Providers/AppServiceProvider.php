<?php

namespace App\Providers;

use App\Projects\MetaData\MetaDataLaravelValidator;
use App\Projects\MetaData\MetaDataValidator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMetaDataValidator();
    }

    /**
     * @return $this
     */
    private function registerMetaDataValidator(): self
    {
        $this->app->singleton(MetaDataValidator::class, MetaDataLaravelValidator::class);

        return $this;
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
