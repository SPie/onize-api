<?php

namespace App\Projects;

use App\Users\UserModel;

interface MemberModelFactory
{
    public function create(UserModel $user, RoleModel $role, array $metaData): MemberModel;
}
