<?php

namespace App\Projects;

use App\Models\AbstractDoctrineModel;
use App\Models\SoftDelete;
use App\Models\Timestamps;
use App\Models\Uuid;
use App\Projects\Invites\InvitationModel;
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
     * @ORM\OneToMany(targetEntity="App\Projects\MemberDoctrineModel", mappedBy="role", cascade={"persist"})
     *
     * @var MemberModel[]|Collection
     */
    private Collection $members;

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
     * @ORM\OneToMany(targetEntity="App\Projects\Invites\InvitationDoctrineModel", mappedBy="role", cascade={"persist"})
     *
     * @var InvitationModel[]|Collection
     */
    private Collection $invitations;

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
        $this->members = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->invitations = new ArrayCollection();
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
     * @param MemberModel[] $members
     *
     * @return RoleModel
     */
    public function setMembers(array $members): RoleModel
    {
        $this->members = new ArrayCollection($members);

        return $this;
    }

    /**
     * @param MemberModel $member
     *
     * @return RoleModel
     */
    public function addMember(MemberModel $member): RoleModel
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
     * @param InvitationModel[] $invitations
     *
     * @return RoleModel
     */
    public function setInvitations(array $invitations): RoleModel
    {
        $this->invitations = new ArrayCollection($invitations);

        return $this;
    }

    /**
     * @param InvitationModel $invitation
     *
     * @return RoleModel
     */
    public function addInvitation(InvitationModel $invitation): RoleModel
    {
        if (!$this->invitations->contains($invitation)) {
            $this->invitations->add($invitation);
        }

        return $this;
    }

    /**
     * @return InvitationModel[]|Collection
     */
    public function getInvitations(): Collection
    {
        return $this->invitations;
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
