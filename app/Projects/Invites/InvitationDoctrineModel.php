<?php

namespace App\Projects\Invites;

use App\Models\AbstractDoctrineModel;
use App\Models\DateTimeCarbonConversion;
use App\Models\Timestamps;
use App\Models\Uuid;
use App\Projects\RoleModel;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class InvitationDoctrineModel
 *
 * @ORM\Table(name="invitations")
 * @ORM\Entity(repositoryClass="App\Projects\Invitations\InvitationDoctrineRepository")
 *
 * @package App\Projects\Invites
 */
final class InvitationDoctrineModel extends AbstractDoctrineModel implements InvitationModel
{
    use DateTimeCarbonConversion;
    use Timestamps;
    use Uuid;

    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Projects\RoleDoctrineModel", inversedBy="invitations", cascade={"persist"})
     *
     * @var RoleModel
     */
    private RoleModel $role;

    /**
     * @ORM\Column(name="valid_until", type="datetime", nullable=false)
     *
     * @var \DateTime
     */
    private \DateTime $validUntil;

    /**
     * @ORM\Column(name="accepted_at", type="datetime", nullable=true)
     *
     * @var \DateTime|null
     */
    private ?\DateTime $acceptedAt;

    /**
     * @ORM\Column(name="declined_at", type="datetime", nullable=true)
     *
     * @var \DateTime|null
     */
    private ?\DateTime $declinedAt;

    /**
     * @ORM\Column(name="meta_data", type="string", length=255, nullable=false)
     *
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
    public function __construct(
        string $uuid,
        RoleModel $role,
        string $email,
        CarbonImmutable $validUntil,
        array $metaData = []
    ) {
        $this->uuid = $uuid;
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
        return [
            self::PROPERTY_UUID        => $this->getUuid(),
            self::PROPERTY_EMAIl       => $this->getEmail(),
            self::PROPERTY_ROLE        => $this->getRole()->toArray(true),
            self::PROPERTY_VALID_UNTIL => $this->getValidUntil()->format('Y-m-d H:i:s'),
            self::PROPERTY_META_DATA   => $this->getMetaData(),
            self::PROPERTY_ACCEPTED_AT => $this->getAcceptedAt() ? $this->getAcceptedAt()->format('Y-m-d H:i:s') : null,
            self::PROPERTY_DECLINED_AT => $this->getDeclinedAt() ? $this->getDeclinedAt()->format('Y-m-d H:i:s') : null,
        ];
    }
}
