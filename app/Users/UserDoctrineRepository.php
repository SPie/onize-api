<?php

namespace App\Users;

use App\Models\AbstractDoctrineRepository;
use App\Models\Model;

/**
 * Class UserDoctrineRepository
 *
 * @package App\Users
 */
final class UserDoctrineRepository extends AbstractDoctrineRepository implements UserRepository
{
    /**
     * @param string $email
     *
     * @return UserModel|Model|null
     */
    public function findOneByEmail(string $email): ?UserModel
    {
        return $this->findOneBy([UserModel::PROPERTY_EMAIL => $email]);
    }
}
