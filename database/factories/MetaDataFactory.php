<?php

use App\Projects\MetaDataDoctrineModel;
use App\Projects\ProjectDoctrineModel;
use App\Users\UserDoctrineModel;
use Faker\Generator as Faker;
use LaravelDoctrine\ORM\Testing\Factory;

/**
 * @var Factory $factory
 */

$factory->define(MetaDataDoctrineModel::class, function (Faker $faker, array $attributes = []) {
    return [
        MetaDataDoctrineModel::PROPERTY_USER    => $attributes[MetaDataDoctrineModel::PROPERTY_USER] ?? entity(UserDoctrineModel::class)->create(),
        MetaDataDoctrineModel::PROPERTY_PROJECT => $attributes[MetaDataDoctrineModel::PROPERTY_PROJECT] ?? entity(ProjectDoctrineModel::class)->create(),
        MetaDataDoctrineModel::PROPERTY_NAME    => $attributes[MetaDataDoctrineModel::PROPERTY_NAME] ?? $faker->word,
        MetaDataDoctrineModel::PROPERTY_VALUE   => $attributes[MetaDataDoctrineModel::PROPERTY_VALUE] ?? $faker->word,
    ];
});
