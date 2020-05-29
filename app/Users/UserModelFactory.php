<?php

namespace App\Users;

/**
 * Interface UserModelFactory
 *
 * @package App\Users
 */
interface UserModelFactory
{
    /**
     * @param string $email
     * @param string $password
     *
     * @return UserModel
     */
    public function create(string $email, string $password): UserModel;
}
