<?php

namespace App\Policies;

use App\Projects\Invites\InvitationModel;
use App\Projects\PermissionModel;
use App\Projects\RoleManager;
use App\Users\UserModel;

final class InvitationPolicy
{
    public function __construct(readonly private RoleManager $roleManager)
    {
    }

    public function accept(UserModel $user, InvitationModel $invitation): bool
    {
        return !$user->isMemberOfProject($invitation->getRole()->getProject())
            && $user->getEmail() === $invitation->getEmail();
    }

    public function decline(UserModel $user, InvitationModel $invitation): bool
    {
        return $user->getEmail() === $invitation->getEmail()
            || $this->roleManager->hasPermissionForAction(
                $invitation->getRole()->getProject(),
                $user,
                PermissionModel::PERMISSION_PROJECTS_INVITATIONS_MANAGEMENT
            );
    }
}
