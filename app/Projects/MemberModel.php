<?php

namespace App\Projects;

use App\Models\Model;
use App\Models\SoftDeletable;
use App\Models\Timestampable;
use App\Users\UserModel;

interface MemberModel extends Model, SoftDeletable, Timestampable
{
    public const PROPERTY_USER      = 'user';
    public const PROPERTY_ROLE      = 'role';
    public const PROPERTY_META_DATA = 'metaData';

    public function getUser(): UserModel;

    public function setRole(RoleModel $role): self;

    public function getRole(): RoleModel;

    public function getMetaData(): array;
}
