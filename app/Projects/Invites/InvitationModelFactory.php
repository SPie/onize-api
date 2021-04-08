<?php

namespace App\Projects\Invites;

use App\Projects\ProjectModel;
use App\Projects\RoleModel;

/**
 * Interface InvitationModelFactory
 *
 * @package App\Projects\Invites
 */
interface InvitationModelFactory
{
    /**
     * @param RoleModel $role
     * @param string    $email
     * @param array     $metaData
     *
     * @return InvitationModel
     */
    public function create(RoleModel $role, string $email, array $metaData = []): InvitationModel;
}
