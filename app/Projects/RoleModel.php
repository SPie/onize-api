<?php

namespace App\Projects;

use App\Models\Model;
use App\Models\SoftDeletable;
use App\Models\Timestampable;
use App\Models\UuidModel;
use App\Projects\Invites\InvitationModel;
use Doctrine\Common\Collections\Collection;

interface RoleModel extends Model, SoftDeletable, Timestampable, UuidModel
{
    public const PROPERTY_LABEL       = 'label';
    public const PROPERTY_OWNER       = 'owner';
    public const PROPERTY_PROJECT     = 'project';
    public const PROPERTY_MEMBERS     = 'members';
    public const PROPERTY_PERMISSIONS = 'permissions';
    public const PROPERTY_INVITATIONS = 'invitations';

    public const LABEL_OWNER = 'Owner';

    public function setLabel(string $label): self;

    public function getLabel(): string;

    public function setOwner(bool $owner): self;

    public function isOwner(): bool;

    public function getProject(): ProjectModel;

    public function setPermissions(array $permissions): self;

    public function addPermission(PermissionModel $permission): self;

    /**
     * @return PermissionModel[]|Collection
     */
    public function getPermissions(): Collection;

    public function hasPermission(string $permissionName): bool;

    public function setMembers(array $members): self;

    public function addMember(MemberModel $member): self;

    /**
     * @return MemberModel[]|Collection
     */
    public function getMembers(): Collection;

    public function setInvitations(array $invitations): self;

    public function addInvitation(InvitationModel $invitation): self;

    /**
     * @return InvitationModel[]|Collection
     */
    public function getInvitations(): Collection;

    public function toArray(bool $withProject = false): array;
}
