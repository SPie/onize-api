<?php

namespace App\Auth;

use App\Models\AbstractDoctrineRepository;

final class RefreshTokenDoctrineRepository extends AbstractDoctrineRepository implements RefreshTokenRepository
{
    public function findOneByRefreshTokenId(string $refreshTokenId): ?RefreshTokenModel
    {
        return $this->findOneBy([RefreshTokenModel::PROPERTY_REFRESH_TOKEN_ID => $refreshTokenId]);
    }
}
