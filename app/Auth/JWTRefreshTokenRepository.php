<?php

namespace App\Auth;

use App\Models\Exceptions\ModelNotFoundException;
use App\Users\UserRepository;
use SPie\LaravelJWT\Contracts\JWT;
use SPie\LaravelJWT\Contracts\RefreshTokenRepository as SPieJWTRefreshTokenRepository;

/**
 * Class JWTRefreshTokenRepository
 *
 * @package App\Auth
 */
final class JWTRefreshTokenRepository implements SPieJWTRefreshTokenRepository
{
    /**
     * @var RefreshTokenRepository
     */
    private RefreshTokenRepository $refreshTokenRepository;

    /**
     * @var RefreshTokenModelFactory
     */
    private RefreshTokenModelFactory $refreshTokenModelFactory;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * JWTRefreshTokenRepository constructor.
     *
     * @param RefreshTokenRepository   $refreshTokenRepository
     * @param RefreshTokenModelFactory $refreshTokenModelFactory
     * @param UserRepository           $userRepository
     */
    public function __construct(
        RefreshTokenRepository $refreshTokenRepository,
        RefreshTokenModelFactory $refreshTokenModelFactory,
        UserRepository $userRepository
    ) {
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->refreshTokenModelFactory = $refreshTokenModelFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @return RefreshTokenRepository
     */
    private function getRefreshTokenRepository(): RefreshTokenRepository
    {
        return $this->refreshTokenRepository;
    }

    /**
     * @return RefreshTokenModelFactory
     */
    private function getRefreshTokenModelFactory(): RefreshTokenModelFactory
    {
        return $this->refreshTokenModelFactory;
    }

    /**
     * @return UserRepository
     */
    private function getUserRepository(): UserRepository
    {
        return $this->userRepository;
    }

    /**
     * @param JWT $refreshToken
     *
     * @return SPieJWTRefreshTokenRepository
     */
    public function storeRefreshToken(JWT $refreshToken): SPieJWTRefreshTokenRepository
    {
        $user = $this->getUserRepository()->findOneByEmail($refreshToken->getSubject());
        if (!$user) {
            throw new ModelNotFoundException(\sprintf('User with email %s not found.', $refreshToken->getSubject()));
        }

        $refreshTokenModel = $this->getRefreshTokenModelFactory()->create(
            $refreshToken->getRefreshTokenId(),
            $user,
            new \DateTime($refreshToken->getExpiresAt()->format('Y-m-d H:i:s'))
        );

        $this->getRefreshTokenRepository()->save($refreshTokenModel);

        return $this;
    }

    /**
     * @param string $refreshTokenId
     *
     * @return SPieJWTRefreshTokenRepository
     */
    public function revokeRefreshToken(string $refreshTokenId): SPieJWTRefreshTokenRepository
    {
        $refreshToken = $this->getRefreshTokenRepository()->findOneByRefreshTokenId($refreshTokenId);
        if (!$refreshToken) {
            throw new ModelNotFoundException(\sprintf('Refresh token with id %s not found.', $refreshTokenId));
        }

        $this->getRefreshTokenRepository()->save($refreshToken->setValidUntil(new \DateTime()));

        return $this;
    }

    /**
     * @param string $refreshTokenId
     *
     * @return bool
     */
    public function isRefreshTokenRevoked(string $refreshTokenId): bool
    {
        $refreshToken = $this->getRefreshTokenRepository()->findOneByRefreshTokenId($refreshTokenId);

        return empty($refreshToken) || ($refreshToken->getValidUntil() < new \DateTime());
    }
}
