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
class InvitationDoctrineModel extends AbstractDoctrineModel implements InvitationModel
{
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
     */
    private \DateTimeImmutable $validUntil;

    /**
     * @ORM\Column(name="accepted_at", type="datetime", nullable=true)
     */
    private ?\DateTimeImmutable $acceptedAt;

    /**
     * @ORM\Column(name="declined_at", type="datetime", nullable=true)
     */
    private ?\DateTimeImmutable $declinedAt;

    /**
     * @ORM\Column(name="meta_data", type="json", nullable=false)
     *
     * @var string
     */
    private array $metaData;

    public function __construct(
        RoleModel $role,
        string $email,
        \DateTimeImmutable $validUntil,
        array $metaData = []
    ) {
        $this->role = $role;
        $this->email = $email;
        $this->validUntil = $validUntil;
        $this->metaData = $metaData;
        $this->acceptedAt = null;
        $this->declinedAt = null;
    }

    public function getRole(): RoleModel
    {
        return $this->role;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getValidUntil(): \DateTimeImmutable
    {
        return $this->validUntil;
    }

    public function getMetaData(): array
    {
        return $this->metaData;
    }

    public function setAcceptedAt(?\DateTimeImmutable $acceptedAt): InvitationModel
    {
        $this->acceptedAt = $acceptedAt;

        return $this;
    }

    public function getAcceptedAt(): ?\DateTimeImmutable
    {
        return $this->acceptedAt;
    }

    public function setDeclinedAt(?\DateTimeImmutable $declinedAt): InvitationModel
    {
        $this->declinedAt = $declinedAt;

        return $this;
    }

    public function getDeclinedAt(): ?\DateTimeImmutable
    {
        return $this->declinedAt;
    }

    public function isExpired(): bool
    {
        return $this->validUntil < new CarbonImmutable();
    }

    public function toArray(): array
    {
        return [
            self::PROPERTY_UUID        => $this->getUuid(),
            self::PROPERTY_EMAIl       => $this->getEmail(),
            self::PROPERTY_ROLE        => $this->getRole()->toArray(true),
            self::PROPERTY_VALID_UNTIL => $this->getValidUntil()->format('Y-m-d H:i:s'),
            self::PROPERTY_META_DATA   => $this->getMetaData(),
            self::PROPERTY_ACCEPTED_AT => $this->getAcceptedAt()?->format('Y-m-d H:i:s'),
            self::PROPERTY_DECLINED_AT => $this->getDeclinedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
