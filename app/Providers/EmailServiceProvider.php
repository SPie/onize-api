<?php

namespace App\Providers;

use App\Emails\EmailService;
use App\Emails\QueuedEmailService;
use Illuminate\Support\ServiceProvider;

/**
 * Class EmailServiceProvider
 *
 * @package App\Providers
 */
final class EmailServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->registerEmailService();
    }

    /**
     * @return $this
     */
    private function registerEmailService(): self
    {
        $this->app->singleton(EmailService::class, QueuedEmailService::class);

        return $this;
    }
}
