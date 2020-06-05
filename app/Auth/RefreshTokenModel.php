<?php

namespace App\Auth;

use App\Models\Model;
use App\Models\Timestampable;
use App\Users\UserModel;

/**
 * Interface RefreshTokenModel
 *
 * @package App\Auth
 */
interface RefreshTokenModel extends Model, Timestampable
{
    const PROPERTY_IDENTIFIER  = 'identifier';
    const PROPERTY_VALID_UNTIL = 'validUntil';
    const PROPERTY_USER        = 'user';

    /**
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @param \DateTime|null $validUntil
     *
     * @return $this
     */
    public function setValidUntil(?\DateTime $validUntil): self;

    /**
     * @return \DateTime|null
     */
    public function getValidUntil(): ?\DateTime;

    /**
     * @return UserModel
     */
    public function getUser(): UserModel;
}
