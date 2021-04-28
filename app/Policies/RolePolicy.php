<?php

namespace App\Policies;

use App\Projects\PermissionModel;
use App\Projects\RoleManager;
use App\Projects\RoleModel;
use App\Users\UserModel;

/**
 * Class RolePolicy
 *
 * @package App\Policies
 */
final class RolePolicy
{
    /**
     * @var RoleManager
     */
    private RoleManager $roleManager;

    /**
     * RolePolicy constructor.
     *
     * @param RoleManager $roleManager
     */
    public function __construct(RoleManager $roleManager)
    {
        $this->roleManager = $roleManager;
    }

    /**
     * @param UserModel $user
     * @param RoleModel $role
     *
     * @return bool
     */
    public function invite(UserModel $user, RoleModel $role): bool
    {
        return $this->roleManager->hasPermissionForAction(
            $role->getProject(),
            $user,
            PermissionModel::PERMISSION_PROJECTS_INVITATIONS_MANAGEMENT
        );
    }
}
