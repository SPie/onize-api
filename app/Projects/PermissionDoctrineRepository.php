<?php

namespace App\Projects;

use App\Models\AbstractDoctrineRepository;
use Doctrine\Common\Collections\Collection;

final class PermissionDoctrineRepository extends AbstractDoctrineRepository implements PermissionRepository
{
    /**
     * @inheritDoc
     */
    public function findByNames(array $names): Collection
    {
        return $this->findBy([PermissionModel::PROPERTY_NAME => $names]);
    }
}
