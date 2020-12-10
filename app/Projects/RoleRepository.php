<?php

namespace App\Projects;

use App\Models\Model;
use App\Models\Repository;

/**
 * Interface RoleRepository
 *
 * @package App\Projects
 */
interface RoleRepository extends Repository
{
    /**
     * @param RoleModel|Model $model
     * @param bool            $flush
     *
     * @return RoleModel|Model
     */
    public function save(Model $model, bool $flush = true): Model;
}
