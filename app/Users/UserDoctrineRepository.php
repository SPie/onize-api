<?php

namespace App\Users;

use App\Models\AbstractDoctrineRepository;
use App\Models\Model;

final class UserDoctrineRepository extends AbstractDoctrineRepository implements UserRepository
{
    public function findOneByEmail(string $email): UserModel|Model|null
    {
        return $this->findOneBy([UserModel::PROPERTY_EMAIL => $email]);
    }

    public function findOneByUuid(string $uuid): UserModel|Model|null
    {
        return $this->findOneBy([UserModel::PROPERTY_UUID => $uuid]);
    }
}
