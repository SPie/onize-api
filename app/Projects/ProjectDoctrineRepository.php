<?php

namespace App\Projects;

use App\Models\AbstractDoctrineRepository;
use App\Models\Model;

/**
 * Class ProjectDoctrineRepository
 *
 * @package App\Projects
 */
final class ProjectDoctrineRepository extends AbstractDoctrineRepository implements ProjectRepository
{
    /**
     * @param string $uuid
     *
     * @return ProjectModel|Model|null
     */
    public function findOneByUuid(string $uuid): ?ProjectModel
    {
        return $this->findOneBy([ProjectModel::PROPERTY_UUID => $uuid]);
    }
}
