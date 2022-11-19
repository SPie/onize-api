<?php

namespace App\Providers;

use App\Emails\EmailService;
use App\Emails\QueuedEmailService;
use Illuminate\Support\ServiceProvider;

final class EmailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerEmailService();
    }

    private function registerEmailService(): self
    {
        $this->app->singleton(EmailService::class, QueuedEmailService::class);

        return $this;
    }
}
