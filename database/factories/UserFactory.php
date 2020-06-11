<?php

use App\Users\UserDoctrineModel;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use LaravelDoctrine\ORM\Testing\Factory;

/**
 * @var Factory $factory
 */


$factory->define(UserDoctrineModel::class, function (Faker $faker, array $attributes = []) {
    return [
        UserDoctrineModel::PROPERTY_UUID           => $attributes[UserDoctrineModel::PROPERTY_UUID] ?? $faker->uuid,
        UserDoctrineModel::PROPERTY_EMAIL          => $attributes[UserDoctrineModel::PROPERTY_EMAIL] ?? $faker->safeEmail,
        UserDoctrineModel::PROPERTY_PASSWORD       => $attributes[UserDoctrineModel::PROPERTY_PASSWORD] ?? Hash::make($faker->password()),
    ];
});
