<?php

namespace App\Auth;

use App\Models\AbstractDoctrineRepository;
use App\Models\Model;

/**
 * Class RefreshTokenDoctrineRepository
 *
 * @package App\Auth
 */
final class RefreshTokenDoctrineRepository extends AbstractDoctrineRepository implements RefreshTokenRepository
{
    /**
     * @param string $refreshTokenId
     *
     * @return RefreshTokenModel|Model|null
     */
    public function findOneByRefreshTokenId(string $refreshTokenId): ?RefreshTokenModel
    {
        return $this->getDatabaseHandler()->load([RefreshTokenDoctrineModel::PROPERTY_IDENTIFIER => $refreshTokenId]);
    }
}
