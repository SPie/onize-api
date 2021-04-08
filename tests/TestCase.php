<?php

namespace Tests;

use Mockery;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Class TestCase
 *
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    use Carbon;
    use Faker;

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();
        $this->clearCarbonMock();

        parent::tearDown();
    }
}
