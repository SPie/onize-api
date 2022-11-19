<?php

namespace App\Projects\Invites;

use App\Projects\RoleModel;
use Carbon\CarbonImmutable;

final class InvitationDoctrineModelFactory implements InvitationModelFactory
{
    public function __construct(readonly private int $validUntilMinutes)
    {
    }

    public function create(RoleModel $role, string $email, array $metaData = []): InvitationModel
    {
        return new InvitationDoctrineModel(
            $role,
            $email,
            (new CarbonImmutable())->addMinutes($this->validUntilMinutes),
            $metaData
        );
    }
}
