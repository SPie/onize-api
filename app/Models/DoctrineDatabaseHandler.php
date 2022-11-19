<?php

namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Persisters\Entity\EntityPersister;

final class DoctrineDatabaseHandler implements DatabaseHandler
{
    public function __construct(readonly private EntityManager $entityManager, readonly private string $className)
    {
    }

    private function getEntityPersister(): EntityPersister
    {
        return $this->entityManager->getUnitOfWork()->getEntityPersister($this->className);
    }

    public function find(int $id): ?Model
    {
        return $this->entityManager->find($this->className, $id);
    }

    public function load(array $criteria): ?Model
    {
        return $this->getEntityPersister()->load($criteria);
    }

    public function loadAll(array $criteria = [], ?array $orderBy = null, int $limit = null, int $offset = null): Collection
    {
        return new ArrayCollection($this->getEntityPersister()->loadAll(
            $criteria,
            $orderBy,
            $limit,
            $offset
        ));
    }

    public function save(Model $model, bool $flush = true): Model
    {
        $this->entityManager->persist($model);

        if ($flush) {
            $this->flush();
        }

        return $model;
    }

    public function delete(Model $model, bool $flush = true): DatabaseHandler
    {
        $this->entityManager->remove($model);

        if ($flush) {
            $this->flush();
        }

        return $this;
    }

    public function flush(): DatabaseHandler
    {
        $this->entityManager->flush();

        return $this;
    }
}
