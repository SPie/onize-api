<?php

namespace App\Users;

use App\Models\Model;
use App\Models\Repository;

interface UserRepository extends Repository
{
    /**
     * @return UserModel|Model
     */
    public function save(Model $model, bool $flush = true): Model;

    public function find(int $id): ?Model;

    public function findOneByEmail(string $email): ?UserModel;

    public function findOneByUuid(string $uuid): ?UserModel;
}
