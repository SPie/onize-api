<?php

namespace App\Projects;

use App\Models\Model;
use App\Models\Repository;

/**
 * Interface ProjectRepository
 *
 * @package App\Projects
 */
interface ProjectRepository extends Repository
{
    /**
     * @param ProjectModel|Model $model
     * @param bool               $flush
     *
     * @return ProjectModel|Model
     */
    public function save(Model $model, bool $flush = true): Model;

    /**
     * @param string $uuid
     *
     * @return ProjectModel|null
     */
    public function findOneByUuid(string $uuid): ?ProjectModel;
}
