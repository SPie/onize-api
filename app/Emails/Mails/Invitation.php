<?php

namespace App\Emails\Mails;

use App\Projects\RoleModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class Invitation extends Mailable
{
    use Queueable;

    public function __construct(string $token, RoleModel $role)
    {
        $this
            ->subject('Project Invitation')
            ->view('emails.projects.invitation', ['token' => $token, 'role' => $role]);
    }
}
