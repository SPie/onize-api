<?php

namespace App\Auth;

use Carbon\CarbonImmutable;
use SPie\LaravelJWT\Contracts\JWT;
use SPie\LaravelJWT\Contracts\RefreshTokenRepository as JWTRefreshTokenRepository;

final class JWTManager implements JWTRefreshTokenRepository
{
    private RefreshTokenRepository $refreshTokenRepository;

    private RefreshTokenModelFactory $refreshTokenModelFactory;

    public function __construct(
        RefreshTokenRepository $refreshTokenRepository,
        RefreshTokenModelFactory $refreshTokenModelFactory
    ) {
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->refreshTokenModelFactory = $refreshTokenModelFactory;
    }

    public function storeRefreshToken(JWT $refreshToken): self
    {
        $this->refreshTokenRepository->save(
            $this->refreshTokenModelFactory->create($refreshToken->getRefreshTokenId())
        );

        return $this;
    }

    public function revokeRefreshToken(string $refreshTokenId): self
    {
        $refreshToken = $this->refreshTokenRepository->findOneByRefreshTokenId($refreshTokenId);
        if ($refreshToken) {
            $this->refreshTokenRepository->save($refreshToken->setRevokedAt(new CarbonImmutable()));
        }

        return $this;
    }

    public function isRefreshTokenRevoked(string $refreshTokenId): bool
    {
        $refreshToken = $this->refreshTokenRepository->findOneByRefreshTokenId($refreshTokenId);

        return !$refreshToken || $refreshToken->isRevoked();
    }
}
