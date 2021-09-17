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
}
