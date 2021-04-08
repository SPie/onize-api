<?php

namespace App\Emails;

use App\Projects\RoleModel;

/**
 * Interface EmailService
 *
 * @package App\Emails
 */
interface EmailService
{
    /**
     * @param string    $email
     * @param string    $token
     * @param RoleModel $role
     *
     * @return $this
     */
    public function sendInvitationEmail(string $email, string $token, RoleModel $role): self;
}
