<?php

namespace App\Projects;

use App\Models\Model;
use App\Models\Repository;

interface RoleRepository extends Repository
{
    public function save(RoleModel|Model $model, bool $flush = true): RoleModel|Model;

    public function findOneByUuid(string $uuid): RoleModel|Model|null;
}
