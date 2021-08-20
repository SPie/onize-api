<?php

namespace App\Projects\Invites;

use App\Models\Model;
use App\Models\Timestampable;
use App\Models\UuidModel;
use App\Projects\RoleModel;
use Carbon\CarbonImmutable;

/**
 * Interface InviteModel
 *
 * @package App\Projects\Invites
 */
interface InvitationModel extends Model, Timestampable, UuidModel
{
    public const PROPERTY_EMAIl       = 'email';
    public const PROPERTY_META_DATA   = 'metaData';
    public const PROPERTY_VALID_UNTIL = 'validUntil';
    public const PROPERTY_ACCEPTED_AT = 'acceptedAt';
    public const PROPERTY_DECLINED_AT = 'declinedAt';
    public const PROPERTY_ROLE        = 'role';

    public function getEmail(): string;

    public function getRole(): RoleModel;

    public function getMetaData(): array;

    public function getValidUntil(): \DateTimeImmutable;

    public function isExpired(): bool;

    public function setAcceptedAt(?\DateTimeImmutable $acceptedAt): self;

    public function getAcceptedAt(): ?\DateTimeImmutable;

    public function setDeclinedAt(?\DateTimeImmutable $declinedAt): self;

    public function getDeclinedAt(): ?\DateTimeImmutable;

    public function toArray(): array;
}
