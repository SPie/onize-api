<?php

namespace App\Projects;

use App\Users\UserModel;

final class MemberDoctrineModelFactory implements MemberModelFactory
{
    public function create(UserModel $user, RoleModel $role, array $metaData): MemberModel
    {
        return new MemberDoctrineModel($user, $role, $metaData);
    }
}
