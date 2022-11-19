<?php

namespace App\Providers;

use App\Projects\MetaData\MetaDataLaravelValidator;
use App\Projects\MetaData\MetaDataValidator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerMetaDataValidator();
    }

    private function registerMetaDataValidator(): self
    {
        $this->app->singleton(MetaDataValidator::class, MetaDataLaravelValidator::class);

        return $this;
    }

    public function boot(): void
    {
        //
    }
}
