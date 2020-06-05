<?php

namespace App\Users;

use App\Auth\RefreshTokenModel;
use App\Models\Model;
use App\Models\SoftDeletable;
use App\Models\Timestampable;
use App\Models\UuidModel;
use Doctrine\Common\Collections\Collection;
use SPie\LaravelJWT\Contracts\JWTAuthenticatable;

/**
 * Interface UserModel
 *
 * @package App\Users
 */
interface UserModel extends Model, JWTAuthenticatable, SoftDeletable, Timestampable, UuidModel
{
    const PROPERTY_EMAIL    = 'email';
    const PROPERTY_PASSWORD = 'password';
    const PROPERTY_REFRESH_TOKENS = 'refreshTokens';

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): self;

    /**
     * @return string
     */
    public function getPassword(): string;

    /**
     * @param RefreshTokenModel[] $refreshTokens
     *
     * @return $this
     */
    public function setRefreshTokens(array $refreshTokens): self;

    /**
     * @param RefreshTokenModel $refreshToken
     *
     * @return $this
     */
    public function addRefreshToken(RefreshTokenModel $refreshToken): self;

    /**
     * @return RefreshTokenModel[]|Collection
     */
    public function getRefreshTokens(): Collection;
}
