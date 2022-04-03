<?php

namespace App\Auth;

use App\Models\Repository;

interface RefreshTokenRepository extends Repository
{
    public function findOneByRefreshTokenId(string $refreshTokenId): ?RefreshTokenModel;
}
