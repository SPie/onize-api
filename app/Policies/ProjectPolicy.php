<?php

namespace App\Policies;

use App\Projects\PermissionModel;
use App\Projects\ProjectModel;
use App\Projects\RoleManager;
use App\Users\UserModel;

/**
 * Class ProjectPolicy
 *
 * @package App\Policies
 */
final class ProjectPolicy
{
    /**
     * @var RoleManager
     */
    private RoleManager $roleManager;

    /**
     * ProjectPolicy constructor.
     *
     * @param RoleManager $roleManager
     */
    public function __construct(RoleManager $roleManager)
    {
        $this->roleManager = $roleManager;
    }

    /**
     * @return RoleManager
     */
    private function getRoleManager(): RoleManager
    {
        return $this->roleManager;
    }

    /**
     * @param UserModel    $user
     * @param ProjectModel $project
     *
     * @return bool
     */
    public function show(UserModel $user, ProjectModel $project): bool
    {
        return $user->isMemberOfProject($project);
    }

    /**
     * @param UserModel    $user
     * @param ProjectModel $project
     *
     * @return bool
     */
    public function members(UserModel $user, ProjectModel $project): bool
    {
        return $this->getRoleManager()->hasPermissionForAction(
            $project,
            $user,
            PermissionModel::PERMISSION_PROJECTS_MEMBERS_SHOW
        );
    }

    /**
     * @param UserModel    $user
     * @param ProjectModel $project
     *
     * @return bool
     */
    public function invite(UserModel $user, ProjectModel $project): bool
    {
        return $this->getRoleManager()->hasPermissionForAction(
            $project,
            $user,
            PermissionModel::PERMISSION_PROJECTS_INVITATIONS_MANAGEMENT
        );
    }
}
