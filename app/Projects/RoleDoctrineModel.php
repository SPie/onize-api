<?php

namespace App\Projects;

use App\Models\AbstractDoctrineModel;
use App\Models\SoftDelete;
use App\Models\Timestamps;
use App\Models\Uuid;
use App\Users\UserModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class RoleDoctrineModel
 *
 * @ORM\Table(name="roles")
 * @ORM\Entity(repositoryClass="App\Projects\RoleDoctrineRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @package App\Projects
 */
final class RoleDoctrineModel extends AbstractDoctrineModel implements RoleModel
{
    use SoftDelete;
    use Timestamps;
    use Uuid;

    /**
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $label;

    /**
     * @ORM\Column(name="owner", type="boolean")
     *
     * @var bool
     */
    private bool $owner;

    /**
     * @ORM\ManyToOne(targetEntity="App\Projects\ProjectDoctrineModel", inversedBy="roles", cascade={"persist"})
     *
     * @var ProjectModel
     */
    private ProjectModel $project;

    /**
     * @ORM\ManyToMany(targetEntity="App\Users\UserDoctrineModel", inversedBy="roles")
     * @ORM\JoinTable(name="roles_users",
     *     joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *     )
     *
     * @var UserModel[]|Collection
     */
    private Collection $users;

    /**
     * @ORM\ManyToMany(targetEntity="App\Projects\PermissionDoctrineModel", inversedBy="roles")
     * @ORM\JoinTable(name="roles_permissions",
     *     joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")}
     *     )
     *
     * @var PermissionModel[]|Collection
     */
    private Collection $permissions;

    /**
     * RoleDoctrineModel constructor.
     *
     * @param string       $uuid
     * @param ProjectModel $project
     * @param string       $label
     * @param bool         $owner
     */
    public function __construct(string $uuid, ProjectModel $project, string $label, bool $owner = false)
    {
        $this->uuid = $uuid;
        $this->project = $project;
        $this->label = $label;
        $this->owner = $owner;
        $this->users = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    /**
     * @param string $label
     *
     * @return $this|RoleModel
     */
    public function setLabel(string $label): RoleModel
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param bool $owner
     *
     * @return $this|RoleModel
     */
    public function setOwner(bool $owner): RoleModel
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return bool
     */
    public function isOwner(): bool
    {
        return $this->owner;
    }

    /**
     * @return ProjectModel
     */
    public function getProject(): ProjectModel
    {
        return $this->project;
    }

    /**
     * @param array $users
     *
     * @return RoleModel
     */
    public function setUsers(array $users): RoleModel
    {
        $this->users = new ArrayCollection($users);

        return $this;
    }

    /**
     * @param UserModel $user
     *
     * @return RoleModel
     */
    public function addUser(UserModel $user): RoleModel
    {
        if (!$this->getUsers()->contains($user)) {
            $this->getUsers()->add($user);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param PermissionModel[] $permissions
     *
     * @return $this
     */
    public function setPermissions(array $permissions): self
    {
        $this->permissions = new ArrayCollection($permissions);

        return $this;
    }

    /**
     * @param PermissionModel $permission
     *
     * @return RoleModel
     */
    public function addPermission(PermissionModel $permission): RoleModel
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }

        return $this;
    }

    /**
     * @return PermissionModel[]|Collection
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    /**
     * @param string $permissionName
     *
     * @return bool
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->getPermissions()->exists(
            fn (int $i, PermissionModel $permission) => $permission->getName() === $permissionName
        );
    }

    /**
     * @param bool $withProject
     *
     * @return array
     */
    public function toArray(bool $withProject = false): array
    {
        $data = [
            self::PROPERTY_UUID  => $this->getUuid(),
            self::PROPERTY_LABEL => $this->getLabel(),
            self::PROPERTY_OWNER => $this->isOwner(),
        ];
        if ($withProject) {
            $data[self::PROPERTY_PROJECT] = $this->getProject()->toArray();
        }

        return $data;
    }
}
