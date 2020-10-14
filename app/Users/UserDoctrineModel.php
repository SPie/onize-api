<?php

namespace App\Users;

use App\Models\AbstractDoctrineModel;
use App\Models\SoftDelete;
use App\Models\Timestamps;
use App\Models\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class UserDoctrineModel
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\User\UserDoctrineRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @package App\Users
 */
class UserDoctrineModel extends AbstractDoctrineModel implements UserModel
{
    use SoftDelete;
    use Timestamps;
    use Uuid;

    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $email;

    /**
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $password;

    /**
     * UserDoctrineModel constructor.
     *
     * @param string $uuid
     * @param string $email
     * @param string $password
     */
    public function __construct(string $uuid, string $email, string $password)
    {
        $this->uuid = $uuid;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string|void
     */
    public function getAuthIdentifierName()
    {
        return self::PROPERTY_ID;
    }

    /**
     * @return mixed|void
     */
    public function getAuthIdentifier()
    {
        return $this->getId();
    }

    /**
     * @return string|void
     */
    public function getAuthPassword()
    {
        return $this->getPassword();
    }

    /**
     * @return string|void
     */
    public function getRememberToken()
    {
        return '';
    }

    /**
     * @param string $value
     */
    public function setRememberToken($value)
    {
    }

    /**
     * @return string|void
     */
    public function getRememberTokenName()
    {
        return '';
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_UUID  => $this->getUuid(),
            self::PROPERTY_EMAIL => $this->getEmail(),
        ];
    }
}
