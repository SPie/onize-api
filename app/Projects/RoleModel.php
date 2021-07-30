<?php

namespace App\Projects;

use App\Models\Model;
use App\Models\SoftDeletable;
use App\Models\Timestampable;
use App\Models\UuidModel;
use App\Projects\Invites\InvitationModel;
use Doctrine\Common\Collections\Collection;

/**
 * Interface RoleModel
 *
 * @package App\Projects
 */
interface RoleModel extends Model, SoftDeletable, Timestampable, UuidModel
{
    public const PROPERTY_LABEL       = 'label';
    public const PROPERTY_OWNER       = 'owner';
    public const PROPERTY_PROJECT     = 'project';
    public const PROPERTY_MEMBERS     = 'members';
    public const PROPERTY_PERMISSIONS = 'permissions';
    public const PROPERTY_INVITATIONS = 'invitations';

    public const LABEL_OWNER = 'Owner';

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label): self;

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @param bool $owner
     *
     * @return $this
     */
    public function setOwner(bool $owner): self;

    /**
     * @return bool
     */
    public function isOwner(): bool;

    /**
     * @return ProjectModel
     */
    public function getProject(): ProjectModel;

    /**
     * @param PermissionModel[] $permissions
     *
     * @return $this
     */
    public function setPermissions(array $permissions): self;

    /**
     * @param PermissionModel $permission
     *
     * @return $this
     */
    public function addPermission(PermissionModel $permission): self;

    /**
     * @return PermissionModel[]|Collection
     */
    public function getPermissions(): Collection;

    /**
     * @param string $permissionName
     *
     * @return bool
     */
    public function hasPermission(string $permissionName): bool;

    /**
     * @param MemberModel[] $members
     *
     * @return $this
     */
    public function setMembers(array $members): self;

    /**
     * @param MemberModel $member
     *
     * @return $this
     */
    public function addMember(MemberModel $member): self;

    /**
     * @return MemberModel[]|Collection
     */
    public function getMembers(): Collection;

    /**
     * @param InvitationModel[] $invitations
     *
     * @return $this
     */
    public function setInvitations(array $invitations): self;

    /**
     * @param InvitationModel $invitation
     *
     * @return $this
     */
    public function addInvitation(InvitationModel $invitation): self;

    /**
     * @return InvitationModel[]|Collection
     */
    public function getInvitations(): Collection;

    /**
     * @param bool $withProject
     *
     * @return array
     */
    public function toArray(bool $withProject = false): array;
}
