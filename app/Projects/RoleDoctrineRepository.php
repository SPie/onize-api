<?php

namespace App\Projects;

use App\Models\AbstractDoctrineRepository;
use App\Models\Model;

/**
 * Class RoleDoctrineRepository
 *
 * @package App\Projects
 */
final class RoleDoctrineRepository extends AbstractDoctrineRepository implements RoleRepository
{
    /**
     * @param string $uuid
     *
     * @return RoleModel|Model|null
     */
    public function findOneByUuid(string $uuid): ?RoleModel
    {
        return $this->findOneBy([RoleModel::PROPERTY_UUID => $uuid]);
    }
}
