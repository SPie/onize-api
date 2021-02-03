<?php

namespace App\Projects;

use App\Models\Model;
use App\Models\SoftDeletable;
use App\Models\Timestampable;
use App\Models\UuidModel;
use App\Users\UserModel;
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
    public const PROPERTY_USERS       = 'users';
    public const PROPERTY_PERMISSIONS = 'permissions';

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
     * @param UserModel[] $users
     *
     * @return $this
     */
    public function setUsers(array $users): self;

    /**
     * @param UserModel $user
     *
     * @return $this
     */
    public function addUser(UserModel $user): self;

    /**
     * @return UserModel[]|Collection
     */
    public function getUsers(): Collection;

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
     * @param bool $withProject
     *
     * @return array
     */
    public function toArray(bool $withProject = false): array;
}
