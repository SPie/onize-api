<?php

namespace App\Users;

use App\Models\AbstractDoctrineModel;
use App\Models\SoftDelete;
use App\Models\Timestamps;
use App\Models\Uuid;
use App\Projects\MetaDataModel;
use App\Projects\ProjectModel;
use App\Projects\RoleModel;
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
     * @ORM\ManyToMany(targetEntity="App\Projects\RoleDoctrineModel", inversedBy="users")
     * @ORM\JoinTable(name="roles_users",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *     )
     *
     * @var RoleModel[]|Collection
     */
    private Collection $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Projects\MetaDataDoctrineModel", mappedBy="user", cascade={"persist"})
     *
     * @var MetaDataModel[]|Collection
     */
    private Collection $metaData;

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
        $this->roles = new ArrayCollection();
        $this->metaData = new ArrayCollection();
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
     * @param RoleModel[] $roles
     *
     * @return $this|UserModel
     */
    public function setRoles(array $roles): UserModel
    {
        $this->roles = new ArrayCollection($roles);

        return $this;
    }

    /**
     * @param RoleModel $role
     *
     * @return $this|UserModel
     */
    public function addRole(RoleModel $role): UserModel
    {
        if (!$this->getRoles()->contains($role)) {
            $this->getRoles()->add($role);
        }

        return $this;
    }

    /**
     * @return RoleModel[]|Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @param MetaDataModel[] $metaData
     *
     * @return UserModel
     */
    public function setMetaData(array $metaData): UserModel
    {
        $this->metaData = new ArrayCollection($metaData);

        return $this;
    }

    /**
     * @param MetaDataModel $metaData
     *
     * @return UserModel
     */
    public function addMetaData(MetaDataModel $metaData): UserModel
    {
        if (!$this->getMetaData()->contains($metaData)) {
            $this->getMetaData()->add($metaData);
        }

        return $this;
    }

    /**
     * @return MetaDataModel[]|Collection
     */
    public function getMetaData(): Collection
    {
        return $this->metaData;
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

    /**
     * @param ProjectModel $project
     *
     * @return bool
     */
    public function isMemberOfProject(ProjectModel $project): bool
    {
        return $this->getRoles()->exists(
            fn (int $i, RoleModel $role) => $role->getProject()->getId() === $project->getId()
        );
    }

    /**
     * @return array
     */
    public function memberData(): array
    {
        $metaDataArray = [];
        foreach ($this->getMetaData() as $metaData) {
            $metaDataArray[$metaData->getName()] = $metaData->getValue();
        }

        return \array_merge(
            $this->toArray(),
            [self::PROPERTY_META_DATA => $metaDataArray]
        );
    }
}
