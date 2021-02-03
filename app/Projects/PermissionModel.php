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
    const PROPERTY_NAME        = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_ROLES       = 'roles';

    const PERMISSION_PROJECTS_MEMBERS_SHOW = 'projects.members.show';

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
