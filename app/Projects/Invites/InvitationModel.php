<?php

namespace App\Projects\Invites;

use App\Models\Model;
use App\Models\SoftDeletable;
use App\Models\Timestampable;
use App\Models\UuidModel;
use App\Projects\RoleModel;
use Carbon\CarbonImmutable;

/**
 * Interface InviteModel
 *
 * @package App\Projects\Invites
 */
interface InvitationModel extends Model, SoftDeletable, Timestampable, UuidModel
{
    public const PROPERTY_EMAIl       = 'email';
    public const PROPERTY_META_DATA   = 'metaData';
    public const PROPERTY_VALID_UNTIL = 'validUntil';
    public const PROPERTY_ACCEPTED_AT = 'acceptedAt';
    public const PROPERTY_DECLINED_AT = 'declined';
    public const PROPERTY_ROLE        = 'role';

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @return RoleModel
     */
    public function getRole(): RoleModel;

    /**
     * @return array
     */
    public function getMetaData(): array;

    /**
     * @return CarbonImmutable
     */
    public function getValidUntil(): CarbonImmutable;

    /**
     * @param CarbonImmutable|null $acceptedAt
     *
     * @return $this
     */
    public function setAcceptedAt(?CarbonImmutable $acceptedAt): self;

    /**
     * @return CarbonImmutable|null
     */
    public function getAcceptedAt(): ?CarbonImmutable;

    /**
     * @param CarbonImmutable|null $declinedAt
     *
     * @return $this
     */
    public function setDeclinedAt(?CarbonImmutable $declinedAt): self;

    /**
     * @return CarbonImmutable|null
     */
    public function getDeclinedAt(): ?CarbonImmutable;
}
