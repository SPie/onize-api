<?php

namespace App\Projects\Invites;

use App\Models\AbstractDoctrineModel;
use App\Models\DateTimeCarbonConversion;
use App\Models\SoftDelete;
use App\Models\Timestamps;
use App\Models\Uuid;
use App\Projects\RoleModel;
use Carbon\CarbonImmutable;

/**
 * Class InvitationDoctrineModel
 *
 * @package App\Projects\Invites
 */
final class InvitationDoctrineModel extends AbstractDoctrineModel implements InvitationModel
{
    use DateTimeCarbonConversion;
    use SoftDelete;
    use Timestamps;
    use Uuid;

    /**
     * @var string
     */
    private string $email;

    /**
     * @var RoleModel
     */
    private RoleModel $role;

    /**
     * @var \DateTime
     */
    private \DateTime $validUntil;

    /**
     * @var \DateTime|null
     */
    private ?\DateTime $acceptedAt;

    /**
     * @var \DateTime|null
     */
    private ?\DateTime $declinedAt;

    /**
     * @var string
     */
    private string $metaData;

    /**
     * InvitationDoctrineModel constructor.
     *
     * @param RoleModel       $role
     * @param string          $email
     * @param CarbonImmutable $validUntil
     * @param array           $metaData
     */
    public function __construct(RoleModel $role, string $email, CarbonImmutable $validUntil, array $metaData = [])
    {
        $this->role = $role;
        $this->email = $email;
        $this->validUntil = $validUntil->toDateTime();
        $this->metaData = \json_encode($metaData);
        $this->acceptedAt = null;
        $this->declinedAt = null;
    }

    /**
     * @return RoleModel
     */
    public function getRole(): RoleModel
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return CarbonImmutable
     */
    public function getValidUntil(): CarbonImmutable
    {
        return $this->convertDateTime($this->validUntil);
    }

    /**
     * @return array
     */
    public function getMetaData(): array
    {
        return \json_decode($this->metaData, true);
    }

    /**
     * @param CarbonImmutable|null $acceptedAt
     *
     * @return InvitationModel
     */
    public function setAcceptedAt(?CarbonImmutable $acceptedAt): InvitationModel
    {
        $this->acceptedAt = $this->convertCarbon($acceptedAt);

        return $this;
    }

    /**
     * @return CarbonImmutable|null
     */
    public function getAcceptedAt(): ?CarbonImmutable
    {
        return $this->convertDateTime($this->acceptedAt);
    }

    /**
     * @param CarbonImmutable|null $declinedAt
     *
     * @return InvitationModel
     */
    public function setDeclinedAt(?CarbonImmutable $declinedAt): InvitationModel
    {
        $this->declinedAt = $this->convertCarbon($declinedAt);

        return $this;
    }

    /**
     * @return CarbonImmutable|null
     */
    public function getDeclinedAt(): ?CarbonImmutable
    {
        return $this->convertDateTime($this->declinedAt);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        // TODO: Implement toArray() method.
    }
}
