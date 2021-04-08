<?php

namespace App\Projects\Invites;

use App\Projects\RoleModel;
use Carbon\CarbonImmutable;

/**
 * Class InvitationDoctrineModelFactory
 *
 * @package App\Projects\Invites
 */
final class InvitationDoctrineModelFactory implements InvitationModelFactory
{
    /**
     * @param RoleModel $role
     * @param string    $email
     * @param array     $metaData
     *
     * @return InvitationModel
     */
    public function create(RoleModel $role, string $email, array $metaData = []): InvitationModel
    {
        return new InvitationDoctrineModel(
            $role,
            $email,
            new CarbonImmutable(),
            $metaData
        );
    }
}
