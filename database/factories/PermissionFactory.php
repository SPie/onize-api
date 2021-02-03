<?php

use App\Projects\PermissionDoctrineModel;
use Faker\Generator as Faker;
use LaravelDoctrine\ORM\Testing\Factory;

/**
 * @var Factory $factory
 */

$factory->define(PermissionDoctrineModel::class, function (Faker $faker, array $attributes = []) {
    return [
        PermissionDoctrineModel::PROPERTY_NAME        => $attributes[PermissionDoctrineModel::PROPERTY_NAME] ?? $faker->uuid,
        PermissionDoctrineModel::PROPERTY_DESCRIPTION => $attributes[PermissionDoctrineModel::PROPERTY_DESCRIPTION] ?? $faker->words(3, true),
    ];
});
