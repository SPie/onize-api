<?php

namespace App\Projects;

/**
 * Interface RoleModelFactory
 *
 * @package App\Projects
 */
interface RoleModelFactory
{
    /**
     * @param ProjectModel $project
     * @param string       $label
     * @param bool         $owner
     *
     * @return RoleModel
     */
    public function create(ProjectModel $project, string $label, bool $owner = false): RoleModel;
}
