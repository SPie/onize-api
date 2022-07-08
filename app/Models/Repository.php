<?php

namespace App\Models;

use Doctrine\Common\Collections\Collection;

interface Repository
{
    public function find(int $id): ?Model;

    public function findAll(): Collection;

    public function findOneBy(array $criteria): ?Model;

    public function findBy(array $criteria = [], ?array $orderBy = null, int $limit = null, int $offset = null): Collection;

    public function save(Model $model, bool $flush = true): Model;

    public function delete(Model $model, bool $flush = true): self;

    public function flush(): Repository;
}
