<?php

namespace Tests;

use Carbon\CarbonImmutable;
use Faker\Factory;
use Faker\Generator;

/**
 * Trait Faker
 *
 * @package Tests
 */
trait Faker
{
    /**
     * @var Generator
     */
    private Generator $faker;

    /**
     * @return Generator
     */
    protected function getFaker(): Generator
    {
        if (!isset($this->faker)) {
            $this->faker = Factory::create();
        }

        return $this->faker;
    }

    /**
     * @return CarbonImmutable
     */
    protected function getCarbonImmutable(): CarbonImmutable
    {
        return new CarbonImmutable($this->getFaker()->dateTime);
    }
}
