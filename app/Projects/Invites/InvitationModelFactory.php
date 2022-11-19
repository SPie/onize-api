<?php

namespace App\Projects\Invites;

use App\Projects\ProjectModel;
use App\Projects\RoleModel;

interface InvitationModelFactory
{
    public function create(RoleModel $role, string $email, array $metaData = []): InvitationModel;
}
