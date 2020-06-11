<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations as EloquentDatabaseMigrations;
use Illuminate\Support\Facades\URL;
use LaravelDoctrine\Migrations\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class FeatureTestCase
 *
 * @package Tests\Feature
 */
abstract class FeatureTestCase extends TestCase
{

    /**
     * @return void
     */
    public function setUpTraits()
    {
        parent::setUpTraits();

        $uses = array_flip(class_uses_recursive(get_class($this)));

        // TODO mock queue service instead of email
        if (isset($uses[DatabaseMigrations::class]) && !isset($uses[EloquentDatabaseMigrations::class])) {
            $this->runDatabaseMigrations();
        }
    }

    /**
     * @param string $routeName
     *
     * @return string
     */
    protected function getUrl(string $routeName): string
    {
        return URL::route($routeName);
    }
}
