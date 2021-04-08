<?php

namespace App\Emails;

use App\Emails\Mails\Invitation;
use App\Projects\RoleModel;

/**
 * Class EmailFactory
 *
 * @package App\Emails
 */
class EmailFactory
{
    /**
     * @param string    $email
     * @param string    $token
     * @param RoleModel $role
     *
     * @return Invitation
     */
    public function createInvitationMail(string $email, string $token, RoleModel $role): Invitation
    {
        return (new Invitation($token, $role))->to($email);
    }
}
