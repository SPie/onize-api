<?php

namespace App\Projects;

use App\Models\UuidGenerator;

/**
 * Class RoleDoctrineModelFactory
 *
 * @package App\Projects
 */
final class RoleDoctrineModelFactory implements RoleModelFactory
{
    public function create(ProjectModel $project, string $label, bool $owner = false): RoleModel
    {
        return new RoleDoctrineModel($project, $label, $owner);
    }
}
