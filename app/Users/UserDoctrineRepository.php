<?php

namespace App\Users;

use App\Models\AbstractDoctrineRepository;

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
     * @return UserModel|null
     */
    public function findOneByEmail(string $email): ?UserModel
    {
        // TODO: Implement findOneByEmail() method.
    }
}
