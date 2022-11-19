<?php

namespace App\Emails;

use App\Projects\RoleModel;

interface EmailService
{
    public function sendInvitationEmail(string $email, string $token, RoleModel $role): self;
}
