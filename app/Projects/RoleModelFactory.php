<?php

namespace App\Projects;

interface RoleModelFactory
{
    public function create(ProjectModel $project, string $label, bool $owner = false): RoleModel;
}
