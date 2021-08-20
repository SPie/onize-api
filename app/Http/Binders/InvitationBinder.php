<?php

namespace App\Http\Binders;

use App\Projects\Invites\InvitationManager;
use App\Projects\Invites\InvitationModel;

final class InvitationBinder
{
    public function __construct(private InvitationManager $invitationManager)
    {
    }

    public function bind(string $uuid): InvitationModel
    {
        return $this->invitationManager->getInvitation($uuid);
    }
}
