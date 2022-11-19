<?php

namespace App\Users;

interface UserModelFactory
{
    public function create(string $email, string $password): UserModel;

    public function setPassword(UserModel $user, string $password): UserModel;
}
