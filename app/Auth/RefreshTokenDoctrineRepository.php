<?php

namespace App\Auth;

use App\Models\AbstractDoctrineRepository;
use App\Models\Model;

final class RefreshTokenDoctrineRepository extends AbstractDoctrineRepository implements RefreshTokenRepository
{
    public function findOneByRefreshTokenId(string $refreshTokenId): RefreshTokenModel|Model|null
    {
        return $this->findOneBy([RefreshTokenModel::PROPERTY_REFRESH_TOKEN_ID => $refreshTokenId]);
    }
}
