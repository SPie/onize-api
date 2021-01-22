<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations as EloquentDatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\URL;
use LaravelDoctrine\Migrations\Testing\DatabaseMigrations;
use Tests\CreatesApplication;
use Tests\Faker;

/**
 * Class FeatureTestCase
 *
 * @package Tests\Feature
 */
abstract class FeatureTestCase extends TestCase
{
    use CreatesApplication;
    use Faker;

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
     * @param array  $parameters
     *
     * @return string
     */
    protected function getUrl(string $routeName, array $parameters = []): string
    {
        return URL::route($routeName, $parameters);
    }
}
