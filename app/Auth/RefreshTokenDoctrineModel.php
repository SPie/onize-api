<?php

namespace App\Auth;

use App\Models\AbstractDoctrineModel;
use App\Models\Timestamps;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class RefreshTokenDoctrineModel
 *
 * @ORM\Table(name="refresh_tokens")
 * @ORM\Entity(repositoryClass="App\Auth\RefreshTokenDoctrineRepository")
 *
 * @package App\Auth
 */
final class RefreshTokenDoctrineModel extends AbstractDoctrineModel implements RefreshTokenModel
{
    use Timestamps;

    /**
     * @ORM\Column(name="refresh_token_id", type="string", length=255, nullable=false)
     */
    private string $refreshTokenId;

    /**
     * @ORM\Column(name="revoked_at", type="datetime", nullable=true)
     */
    private ?\DateTimeImmutable $revokedAt;

    public function __construct(string $refreshTokenId)
    {
        $this->refreshTokenId = $refreshTokenId;
        $this->revokedAt = null;
    }

    public function getRefreshTokenId(): string
    {
        return $this->refreshTokenId;
    }

    public function setRevokedAt(?\DateTimeImmutable $revokedAt): RefreshTokenModel
    {
        $this->revokedAt = $revokedAt;

        return $this;
    }

    public function getRevokedAt(): ?\DateTimeImmutable
    {
        return $this->revokedAt;
    }

    public function isRevoked(): bool
    {
        return $this->revokedAt && $this->revokedAt <= new CarbonImmutable();
    }
}
