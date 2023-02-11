<?php

namespace Tests;

trait DatabaseMigrations
{
    public function setUpDatabaseMigrations(): void
    {
        $this->artisan('doctrine:migrations:refresh');
//        $this->artisan('doctrine:migrations:reset');
//        $this->artisan('doctrine:migrations:migrate');
    }
}
