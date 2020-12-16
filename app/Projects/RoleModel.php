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
    const PROPERTY_LABEL   = 'label';
    const PROPERTY_OWNER   = 'owner';
    const PROPERTY_PROJECT = 'project';
    const PROPERTY_USERS   = 'users';

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
     * @return array
     */
    public function toArray(): array;
}
