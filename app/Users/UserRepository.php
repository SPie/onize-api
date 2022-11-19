<?php

namespace App\Users;

use App\Models\Model;
use App\Models\Repository;

interface UserRepository extends Repository
{
    public function save(UserModel|Model $model, bool $flush = true): UserModel|Model;

    public function find(int $id): UserModel|Model|null;

    public function findOneByEmail(string $email): UserModel|Model|null;

    public function findOneByUuid(string $uuid): UserModel|Model|null;
}
