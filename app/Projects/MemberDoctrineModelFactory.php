<?php

namespace App\Projects;

use App\Users\UserModel;

/**
 * Class MemberDoctrineModelFactory
 *
 * @package App\Projects
 */
final class MemberDoctrineModelFactory implements MemberModelFactory
{
    /**
     * @param UserModel $user
     * @param RoleModel $role
     * @param array     $metaData
     *
     * @return MemberModel
     */
    public function create(UserModel $user, RoleModel $role, array $metaData): MemberModel
    {
        return new MemberDoctrineModel($user, $role, $metaData);
    }
}
