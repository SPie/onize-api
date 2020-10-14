<?php

namespace App\Users;

use App\Models\Model;
use App\Models\SoftDeletable;
use App\Models\Timestampable;
use App\Models\UuidModel;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Interface UserModel
 *
 * @package App\Users
 */
interface UserModel extends Model, Authenticatable, SoftDeletable, Timestampable, UuidModel
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
     * @return array
     */
    public function toArray(): array;
}
