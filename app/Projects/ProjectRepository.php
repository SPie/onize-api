<?php

namespace App\Projects;

use App\Models\Model;
use App\Models\Repository;

interface ProjectRepository extends Repository
{
    public function save(ProjectModel|Model $model, bool $flush = true): ProjectModel|Model;

    public function findOneByUuid(string $uuid): ProjectModel|Model|null;
}
