<?php

namespace App\Policies;

use App\Projects\ProjectModel;
use App\Users\UserModel;

/**
 * Class ProjectPolicy
 *
 * @package App\Policies
 */
final class ProjectPolicy
{
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
}
