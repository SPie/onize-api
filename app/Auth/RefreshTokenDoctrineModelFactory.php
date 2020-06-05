<?php

namespace App\Auth;

use App\Users\UserModel;

/**
 * Class RefreshTokenDoctrineModelFactory
 *
 * @package App\Auth
 */
final class RefreshTokenDoctrineModelFactory implements RefreshTokenModelFactory
{
    /**
     * @param string         $identifier
     * @param UserModel      $user
     * @param \DateTime|null $validUntil
     *
     * @return RefreshTokenModel
     */
    public function create(string $identifier, UserModel $user, \DateTime $validUntil = null): RefreshTokenModel
    {
        return new RefreshTokenDoctrineModel($identifier, $user, $validUntil);
    }
}
