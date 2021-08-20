<?php

namespace App\Policies;

use App\Projects\Invites\InvitationModel;
use App\Users\UserModel;

final class InvitationPolicy
{
    public function accept(UserModel $user, InvitationModel $invitation): bool
    {
        return !$user->isMemberOfProject($invitation->getRole()->getProject())
            && $user->getEmail() == $invitation->getEmail();
    }
}
