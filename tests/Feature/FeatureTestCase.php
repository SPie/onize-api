<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\URL;
use Tests\Carbon;
use Tests\CreatesApplication;
use Tests\DatabaseMigrations;
use Tests\Faker;

/**
 * Class FeatureTestCase
 *
 * @package Tests\Feature
 */
abstract class FeatureTestCase extends TestCase
{
    use Carbon;
    use CreatesApplication;
    use DatabaseMigrations;
    use Faker;

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
