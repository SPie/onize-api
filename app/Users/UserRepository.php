<?php

namespace App\Users;

use App\Models\Model;
use App\Models\Repository;

interface UserRepository extends Repository
{
    public function save(Model $model, bool $flush = true): Model;

    /**
     * @return UserModel|Model|null
     */
    public function find(int $id): ?Model;

    public function findOneByEmail(string $email): ?UserModel;

    public function findOneByUuid(string $uuid): ?UserModel;
}
