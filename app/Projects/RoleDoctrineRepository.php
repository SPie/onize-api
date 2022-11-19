<?php

namespace App\Projects;

use App\Models\AbstractDoctrineRepository;
use App\Models\Model;

final class RoleDoctrineRepository extends AbstractDoctrineRepository implements RoleRepository
{
    public function findOneByUuid(string $uuid): RoleModel|Model|null
    {
        return $this->findOneBy([RoleModel::PROPERTY_UUID => $uuid]);
    }
}
