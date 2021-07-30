<?php

namespace App\Projects;

use App\Users\UserModel;

/**
 * Interface MemberModelFactory
 *
 * @package App\Projects
 */
interface MemberModelFactory
{
    /**
     * @param UserModel $user
     * @param RoleModel $role
     * @param array     $metaData
     *
     * @return MemberModel
     */
    public function create(UserModel $user, RoleModel $role, array $metaData): MemberModel;
}
