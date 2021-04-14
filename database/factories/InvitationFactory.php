<?php

use App\Projects\Invites\InvitationDoctrineModel;
use App\Projects\RoleDoctrineModel;
use Faker\Generator as Faker;
use LaravelDoctrine\ORM\Testing\Factory;

/**
 * @var Factory $factory
 */

$factory->define(InvitationDoctrineModel::class, function (Faker $faker, array $attributes = []) {
    return [
        InvitationDoctrineModel::PROPERTY_UUID        => $attributes[InvitationDoctrineModel::PROPERTY_UUID] ?? $faker->uuid,
        InvitationDoctrineModel::PROPERTY_EMAIl       => $attributes[InvitationDoctrineModel::PROPERTY_EMAIl] ?? $faker->safeEmail,
        InvitationDoctrineModel::PROPERTY_ROLE        => $attributes[InvitationDoctrineModel::PROPERTY_ROLE] ?? entity(RoleDoctrineModel::class)->create(),
        InvitationDoctrineModel::PROPERTY_VALID_UNTIL => $attributes[InvitationDoctrineModel::PROPERTY_VALID_UNTIL] ?? $faker->dateTime,
        InvitationDoctrineModel::PROPERTY_META_DATA   => $attributes[InvitationDoctrineModel::PROPERTY_META_DATA] ?? [],
        InvitationDoctrineModel::PROPERTY_ACCEPTED_AT => $attributes[InvitationDoctrineModel::PROPERTY_ACCEPTED_AT] ?? null,
        InvitationDoctrineModel::PROPERTY_DECLINED_AT => $attributes[InvitationDoctrineModel::PROPERTY_DECLINED_AT] ?? null,
    ];
});
