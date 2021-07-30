<?php

namespace App\Users;

use App\Models\Model;
use App\Models\SoftDeletable;
use App\Models\Timestampable;
use App\Models\UuidModel;
use App\Projects\MemberModel;
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
    public const PROPERTY_MEMBERS        = 'members';

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
     * @param ProjectModel $project
     *
     * @return RoleModel|null
     */
    public function getRoleForProject(ProjectModel $project): ?RoleModel;

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
}
