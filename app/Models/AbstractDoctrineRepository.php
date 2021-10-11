<?php

namespace App\Models;

use Doctrine\Common\Collections\Collection;

abstract class AbstractDoctrineRepository implements Repository
{
    public function __construct(private DatabaseHandler $databaseHandler)
    {
    }

    public function find(int $id): ?Model
    {
        return $this->databaseHandler->find($id);
    }

    public function findAll(): Collection
    {
        return $this->databaseHandler->loadAll();
    }

    public function findBy(array $criteria = [], ?array $orderBy = null, int $limit = null, int $offset = null): Collection
    {
        return $this->databaseHandler->loadAll($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria): ?Model
    {
        return $this->databaseHandler->load($criteria);
    }

    public function save(Model $model, bool $flush = true): Model
    {
        return $this->databaseHandler->save($model, $flush);
    }

    public function delete(Model $model, bool $flush = true): Repository
    {
        $this->databaseHandler->delete($model, $flush);

        return $this;
    }

    public function flush(): Repository
    {
        $this->databaseHandler->flush();

        return $this;
    }
}
