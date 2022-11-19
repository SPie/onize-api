<?php

namespace App\Projects;

use App\Models\AbstractDoctrineRepository;
use App\Models\Model;

final class ProjectDoctrineRepository extends AbstractDoctrineRepository implements ProjectRepository
{
    public function findOneByUuid(string $uuid): ProjectModel|Model|null
    {
        return $this->findOneBy([ProjectModel::PROPERTY_UUID => $uuid]);
    }
}
