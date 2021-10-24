<?php

namespace App\Policies;

use App\Projects\PermissionModel;
use App\Projects\RoleManager;
use App\Projects\RoleModel;
use App\Users\UserModel;

final class RolePolicy
{
    public function __construct(private RoleManager $roleManager)
    {
    }

    public function invite(UserModel $user, RoleModel $role): bool
    {
        return $this->roleManager->hasPermissionForAction(
            $role->getProject(),
            $user,
            PermissionModel::PERMISSION_PROJECTS_INVITATIONS_MANAGEMENT
        );
    }

    public function removeRole(UserModel $user, RoleModel $role): bool
    {
        return !$role->isOwner() && $this->roleManager->hasPermissionForAction(
            $role->getProject(),
            $user,
            PermissionModel::PERMISSION_PROJECTS_ROLES_MANAGEMENT
        );
    }
}
