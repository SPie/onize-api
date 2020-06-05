<?php

namespace App\Auth;

use App\Users\UserModel;

/**
 * Interface RefreshTokenModelFactory
 *
 * @package App\Auth
 */
interface RefreshTokenModelFactory
{
    /**
     * @param string         $identifier
     * @param UserModel      $user
     * @param \DateTime|null $validUntil
     *
     * @return RefreshTokenModel
     */
    public function create(string $identifier, UserModel $user, \DateTime $validUntil = null): RefreshTokenModel;
}
