<?php

namespace App\Policies;

use App\Projects\PermissionModel;
use App\Projects\ProjectModel;
use App\Projects\RoleManager;
use App\Users\UserModel;

final class ProjectPolicy
{
    public function __construct(private RoleManager $roleManager)
    {
    }

    public function show(UserModel $user, ProjectModel $project): bool
    {
        return $user->isMemberOfProject($project);
    }

    public function members(UserModel $user, ProjectModel $project): bool
    {
        return $this->roleManager->hasPermissionForAction(
            $project,
            $user,
            PermissionModel::PERMISSION_PROJECTS_MEMBERS_SHOW
        );
    }

    public function removeMember(UserModel $user, ProjectModel $project, UserModel $member): bool
    {
        return (
                !$member->getRoleForProject($project)
                || !$member->getRoleForProject($project)->isOwner()
                || $user->getRoleForProject($project)->isOwner()
            )
            && $this->roleManager->hasPermissionForAction(
                $project,
                $user,
                PermissionModel::PERMISSION_PROJECTS_MEMBER_MANAGEMENT
            );
    }

    public function invite(UserModel $user, ProjectModel $project): bool
    {
        return $this->roleManager->hasPermissionForAction(
            $project,
            $user,
            PermissionModel::PERMISSION_PROJECTS_INVITATIONS_MANAGEMENT
        );
    }

    public function createRole(UserModel $user, ProjectModel $project): bool
    {
        return $this->roleManager->hasPermissionForAction(
            $project,
            $user,
            PermissionModel::PERMISSION_PROJECTS_ROLES_MANAGEMENT
        );
    }

    public function changeRole(UserModel $user, ProjectModel $project, UserModel $memberUser): bool
    {
        if ($user->getMemberOfProject($project)->getRole()->isOwner()) {
            return true;
        }

        if ($memberUser->getMemberOfProject($project)->getRole()->isOwner()) {
            return false;
        }

        return $this->roleManager->hasPermissionForAction(
            $project,
            $user,
            PermissionModel::PERMISSION_PROJECTS_ROLES_MANAGEMENT
        );
    }
}
