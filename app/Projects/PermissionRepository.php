<?php

namespace App\Projects;

use App\Models\Repository;
use Doctrine\Common\Collections\Collection;

interface PermissionRepository extends Repository
{
    /**
     * @return PermissionModel[]|Collection
     */
    public function findByNames(array $names): Collection;
}
