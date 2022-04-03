<?php

namespace App\Auth;

interface RefreshTokenModelFactory
{
    public function create(string $refreshTokenId): RefreshTokenModel;
}
