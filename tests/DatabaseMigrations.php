<?php

namespace Tests;

trait DatabaseMigrations
{
    public function setUpDatabaseMigrations(): void
    {
        $this->artisan('doctrine:migrations:refresh', ['--no-interaction' => true]);
    }
}
