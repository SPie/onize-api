<?php

namespace App\Auth;

use App\Models\Model;
use App\Models\Timestampable;

interface RefreshTokenModel extends Model, Timestampable
{
    public const PROPERTY_REFRESH_TOKEN_ID = 'refreshTokenId';
    public const PROPERTY_REVOKED_AT       = 'revokedAt';

    public function getRefreshTokenId(): string;

    public function setRevokedAt(?\DateTimeImmutable $revokedAt): self;

    public function getRevokedAt(): ?\DateTimeImmutable;

    public function isRevoked(): bool;
}
