<?php

namespace App\Users;

use App\Models\Model;
use App\Models\SoftDeletable;
use App\Models\Timestampable;
use App\Models\UuidModel;
use App\Projects\MetaDataModel;
use App\Projects\ProjectModel;
use App\Projects\RoleModel;
use Doctrine\Common\Collections\Collection;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Interface UserModel
 *
 * @package App\Users
 */
interface UserModel extends Model, Authenticatable, SoftDeletable, Timestampable, UuidModel
{
    public const PROPERTY_EMAIL          = 'email';
    public const PROPERTY_PASSWORD       = 'password';
    public const PROPERTY_REFRESH_TOKENS = 'refreshTokens';
    public const PROPERTY_ROLES          = 'roles';
    public const PROPERTY_META_DATA      = 'metaData';

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): self;

    /**
     * @return string
     */
    public function getPassword(): string;

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

    /**
     * @param ProjectModel $project
     *
     * @return RoleModel|null
     */
    public function getRoleForProject(ProjectModel $project): ?RoleModel;

    /**
     * @param MetaDataModel[] $metaData
     *
     * @return $this
     */
    public function setMetaData(array $metaData): self;

    /**
     * @param MetaDataModel $metaData
     *
     * @return $this
     */
    public function addMetaData(MetaDataModel $metaData): self;

    /**
     * @return MetaDataModel[]|Collection
     */
    public function getMetaData(): Collection;

    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @param ProjectModel $project
     *
     * @return bool
     */
    public function isMemberOfProject(ProjectModel $project): bool;

    /**
     * @return array
     */
    public function memberData(): array;
}
