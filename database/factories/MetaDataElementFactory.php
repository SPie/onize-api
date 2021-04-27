<?php

use App\Projects\MetaDataElementDoctrineModel;
use App\Projects\ProjectDoctrineModel;
use Faker\Generator as Faker;
use LaravelDoctrine\ORM\Testing\Factory;

/**
 * @var Factory $factory
 */

$factory->define(MetaDataElementDoctrineModel::class, function (Faker $faker, array $attributes = []) {
    return [
        MetaDataElementDoctrineModel::PROPERTY_PROJECT  => $attributes[MetaDataElementDoctrineModel::PROPERTY_PROJECT] ?? entity(ProjectDoctrineModel::class)->create(),
        MetaDataElementDoctrineModel::PROPERTY_NAME     => $attributes[MetaDataElementDoctrineModel::PROPERTY_NAME] ?? $faker->uuid,
        MetaDataElementDoctrineModel::PROPERTY_LABEL    => $attributes[MetaDataElementDoctrineModel::PROPERTY_LABEL] ?? $faker->word,
        MetaDataElementDoctrineModel::PROPERTY_REQUIRED => $attributes[MetaDataElementDoctrineModel::PROPERTY_REQUIRED] ?? $faker->boolean,
        MetaDataElementDoctrineModel::PROPERTY_IN_LIST  => $attributes[MetaDataElementDoctrineModel::PROPERTY_IN_LIST] ?? $faker->boolean,
        MetaDataElementDoctrineModel::PROPERTY_TYPE     => $attributes[MetaDataElementDoctrineModel::PROPERTY_TYPE] ?? 'string',
    ];
});
