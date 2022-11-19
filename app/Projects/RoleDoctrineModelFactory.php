<?php

namespace App\Projects;

final class RoleDoctrineModelFactory implements RoleModelFactory
{
    public function create(ProjectModel $project, string $label, bool $owner = false): RoleModel
    {
        return new RoleDoctrineModel($project, $label, $owner);
    }
}
