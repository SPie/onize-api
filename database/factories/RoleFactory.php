<?php

use App\Projects\ProjectDoctrineModel;
use App\Projects\RoleDoctrineModel;
use Faker\Generator as Faker;
use LaravelDoctrine\ORM\Testing\Factory;

/**
 * @var Factory $factory
 */

$factory->define(RoleDoctrineModel::class, function (Faker $faker, array $attributes = []) {
    return [
        RoleDoctrineModel::PROPERTY_UUID    => $attributes[RoleDoctrineModel::PROPERTY_UUID] ?? $faker->uuid,
        RoleDoctrineModel::PROPERTY_LABEL   => $attributes[RoleDoctrineModel::PROPERTY_LABEL] ?? $faker->word,
        RoleDoctrineModel::PROPERTY_OWNER   => $attributes[RoleDoctrineModel::PROPERTY_OWNER] ?? $faker->boolean,
        RoleDoctrineModel::PROPERTY_PROJECT => $attributes[RoleDoctrineModel::PROPERTY_PROJECT] ?? entity(ProjectDoctrineModel::class)->create(),
    ];
});
