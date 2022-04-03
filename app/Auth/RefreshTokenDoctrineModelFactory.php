<?php

namespace App\Auth;

final class RefreshTokenDoctrineModelFactory implements RefreshTokenModelFactory
{
    public function create(string $refreshTokenId): RefreshTokenModel
    {
        return new RefreshTokenDoctrineModel($refreshTokenId);
    }
}
