<?php

namespace App\Auth;

use App\Models\Model;
use App\Models\Repository;

interface RefreshTokenRepository extends Repository
{
    public function findOneByRefreshTokenId(string $refreshTokenId): RefreshTokenModel|Model|null;
}
