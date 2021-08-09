<?php

namespace App\Users;

use App\Models\PasswordHasher;

/**
 * Class UserDoctrineModelFactory
 *
 * @package App\Users
 */
final class UserDoctrineModelFactory implements UserModelFactory
{
    public function __construct(private PasswordHasher $passwordHasher)
    {
    }

    public function create(string $email, string $password): UserModel
    {
        return new UserDoctrineModel($email, $this->passwordHasher->hash($password));
    }

    public function setPassword(UserModel $user, string $password): UserModel
    {
        return $user->setPassword($this->passwordHasher->hash($password));
    }
}
