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

interface UserModel extends Model, Authenticatable, SoftDeletable, Timestampable, UuidModel
{
    public const PROPERTY_EMAIL          = 'email';
    public const PROPERTY_PASSWORD       = 'password';
    public const PROPERTY_MEMBERS        = 'members';

    public function setEmail(string $email): self;

    public function getEmail(): string;

    public function setPassword(string $password): self;

    public function getPassword(): string;

    /**
     * @param MemberModel[] $members
     */
    public function setMembers(array $members): self;

    public function addMember(MemberModel $member): self;

    /**
     * @return MemberModel[]|Collection
     */
    public function getMembers(): Collection;

    public function getRoleForProject(ProjectModel $project): ?RoleModel;

    public function toArray(): array;

    public function isMemberOfProject(ProjectModel $project): bool;

    public function getMemberOfProject(ProjectModel $project): ?MemberModel;
}
