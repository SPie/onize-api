<?php

namespace App\Users;

use App\Models\PasswordHasher;

final class UserDoctrineModelFactory implements UserModelFactory
{
    public function __construct(readonly private PasswordHasher $passwordHasher)
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
