<?php

namespace App\Emails;

use App\Emails\Mails\Invitation;
use App\Projects\RoleModel;

class EmailFactory
{
    public function createInvitationMail(string $email, string $token, RoleModel $role): Invitation
    {
        return (new Invitation($token, $role))->to($email);
    }
}
