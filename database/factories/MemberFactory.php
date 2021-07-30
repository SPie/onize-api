<?php

use App\Projects\MemberDoctrineModel;
use App\Projects\RoleDoctrineModel;
use App\Users\UserDoctrineModel;
use Faker\Generator as Faker;
use LaravelDoctrine\ORM\Testing\Factory;

/**
 * @var Factory $factory
 */

$factory->define(MemberDoctrineModel::class, function (Faker $faker, array $attributes = []) {
    return [
        MemberDoctrineModel::PROPERTY_USER      => $attributes[MemberDoctrineModel::PROPERTY_USER] ?? entity(UserDoctrineModel::class)->create(),
        MemberDoctrineModel::PROPERTY_ROLE      => $attributes[MemberDoctrineModel::PROPERTY_ROLE] ?? entity(RoleDoctrineModel::class)->create(),
        MemberDoctrineModel::PROPERTY_META_DATA => $attributes[MemberDoctrineModel::PROPERTY_META_DATA] ?? \json_encode([]),
    ];
});
