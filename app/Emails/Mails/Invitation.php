<?php

namespace App\Emails\Mails;

use App\Projects\RoleModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

/**
 * Class Invitation
 *
 * @package App\Emails\Mails
 */
class Invitation extends Mailable
{
    use Queueable;

    /**
     * Invitation constructor.
     *
     * @param string    $token
     * @param RoleModel $role
     */
    public function __construct(string $token, RoleModel $role)
    {
        $this
            ->subject('Project Invitation')
            ->view('emails.projects.invitation', ['token' => $token, 'role' => $role]);
    }
}
