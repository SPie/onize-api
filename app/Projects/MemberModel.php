<?php

namespace App\Projects;

use App\Models\Model;
use App\Models\SoftDeletable;
use App\Models\Timestampable;
use App\Users\UserModel;

/**
 * Interface MemberModel
 *
 * @package App\Projects
 */
interface MemberModel extends Model, SoftDeletable, Timestampable
{
    public const PROPERTY_USER      = 'user';
    public const PROPERTY_ROLE      = 'role';
    public const PROPERTY_META_DATA = 'metaData';

    /**
     * @return UserModel
     */
    public function getUser(): UserModel;

    /**
     * @return RoleModel
     */
    public function getRole(): RoleModel;

    /**
     * @return array
     */
    public function getMetaData(): array;
}
