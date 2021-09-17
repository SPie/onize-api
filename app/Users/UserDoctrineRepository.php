<?php

namespace App\Users;

use App\Models\AbstractDoctrineRepository;
use App\Models\Model;

final class UserDoctrineRepository extends AbstractDoctrineRepository implements UserRepository
{
    /**
     * @return UserModel|Model|null
     */
    public function findOneByEmail(string $email): ?UserModel
    {
        return $this->findOneBy([UserModel::PROPERTY_EMAIL => $email]);
    }

    /**
     * @return UserModel|Model|null
     */
    public function findOneByUuid(string $uuid): ?UserModel
    {
        return $this->findOneBy([UserModel::PROPERTY_UUID => $uuid]);
    }
}
