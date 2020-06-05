<?php

namespace App\Auth;

use App\Models\Repository;

/**
 * Interface RefreshTokenRepository
 *
 * @package App\Auth
 */
interface RefreshTokenRepository extends Repository
{
    /**
     * @param string $refreshTokenId
     *
     * @return RefreshTokenModel|null
     */
    public function findOneByRefreshTokenId(string $refreshTokenId): ?RefreshTokenModel;
}
