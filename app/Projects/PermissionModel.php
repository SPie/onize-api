<?php

namespace App\Projects;

use App\Models\Model;
use Doctrine\Common\Collections\Collection;

/**
 * Interface PermissionModel
 *
 * @package App\Projects
 */
interface PermissionModel extends Model
{
    public const PROPERTY_NAME        = 'name';
    public const PROPERTY_DESCRIPTION = 'description';
    public const PROPERTY_ROLES       = 'roles';

    public const PERMISSION_PROJECTS_MEMBERS_SHOW           = 'projects.members.show';
    public const PERMISSION_PROJECTS_MEMBER_MANAGEMENT      = 'projects.members.management';
    public const PERMISSION_PROJECTS_INVITATIONS_MANAGEMENT = 'projects.invitations.management';

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @param RoleModel[] $roles
     *
     * @return $this
     */
    public function setRoles(array $roles): self;

    /**
     * @param RoleModel $role
     *
     * @return $this
     */
    public function addRole(RoleModel $role): self;

    /**
     * @return RoleModel[]|Collection
     */
    public function getRoles(): Collection;
}
