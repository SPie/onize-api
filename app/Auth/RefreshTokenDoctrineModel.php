<?php

namespace App\Auth;

use App\Models\AbstractDoctrineModel;
use App\Models\Timestamps;
use App\Users\UserModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class RefreshTokenDoctrineModel
 *
 * @ORM\Table(name="refresh_tokens")
 * @ORM\Entity(repositoryClass="App\Auth\RefreshTokenDoctrineRepository")
 *
 * @package App\Auth
 */
class RefreshTokenDoctrineModel extends AbstractDoctrineModel implements RefreshTokenModel
{
    use Timestamps;

    /**
     * @ORM\Column(name="identifier", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $identifier;

    /**
     * @ORM\ManyToOne(targetEntity="App\Users\UserDoctrineModel", inversedBy="refreshTokens", cascade={"persist"})
     *
     * @var UserModel
     */
    private UserModel $user;

    /**
     * @ORM\Column(name="valid_until", type="datetime", nullable=true)
     *
     * @var \DateTime|null
     */
    private ?\DateTime $validUntil = null;

    /**
     * RefreshTokenDoctrineModel constructor.
     *
     * @param string         $identifier
     * @param UserModel      $user
     * @param \DateTime|null $validUntil
     */
    public function __construct(string $identifier, UserModel $user, \DateTime $validUntil = null)
    {
        $this->identifier = $identifier;
        $this->user = $user;
        $this->validUntil = $validUntil;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return UserModel
     */
    public function getUser(): UserModel
    {
        return $this->user;
    }

    /**
     * @param \DateTime|null $validUntil
     *
     * @return $this
     */
    public function setValidUntil(?\DateTime $validUntil): self
    {
        $this->validUntil = $validUntil;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getValidUntil(): ?\DateTime
    {
        return $this->validUntil;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [];
    }
}
