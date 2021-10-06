<?php

namespace App\Models;

use Doctrine\Common\Collections\Collection;

interface DatabaseHandler
{
    public function find(int $id): ?Model;

    public function load(array $criteria): ?Model;

    public function loadAll(array $criteria = [], ?array $orderBy = null, int $limit = null, int $offset = null): Collection;

    public function save(Model $model, bool $flush = true): Model;

    public function delete(Model $model, bool $flush = true): self;

    public function flush(): self;
}
