<?php

namespace App\Users;

use App\Models\AbstractDoctrineModel;
use App\Models\SoftDelete;
use App\Models\Timestamps;
use App\Models\Uuid;
use App\Projects\MemberModel;
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
     * @ORM\OneToMany(targetEntity="App\Projects\MemberDoctrineModel", mappedBy="user", cascade={"persist"})
     *
     * @var MemberModel[]|Collection
     */
    private Collection $members;

    /**
     * UserDoctrineModel constructor.
     *
     * @param string $email
     * @param string $password
     */
    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
        $this->members = new ArrayCollection();
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
     * @param MemberModel[] $members
     *
     * @return UserModel
     */
    public function setMembers(array $members): UserModel
    {
        $this->members = new ArrayCollection($members);

        return $this;
    }

    /**
     * @param MemberModel $member
     *
     * @return UserModel
     */
    public function addMember(MemberModel $member): UserModel
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    /**
     * @return MemberModel[]|Collection
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /**
     * @param ProjectModel $project
     *
     * @return RoleModel|null
     */
    public function getRoleForProject(ProjectModel $project): ?RoleModel
    {
        foreach ($this->getMembers() as $member) {
            if ($member->getRole()->getProject()->getId() === $project->getId()) {
                return $member->getRole();
            }
        }

        return null;
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
        return $this->getMembers()->exists(
            fn (int $i, MemberModel $member) => $member->getRole()->getProject()->getId() === $project->getId()
        );
    }
}
