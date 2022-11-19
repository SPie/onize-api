<?php

namespace App\Emails;

use App\Projects\RoleModel;
use Illuminate\Contracts\Mail\MailQueue;

final class QueuedEmailService implements EmailService
{
    private const QUEUE_NAME_USER_EMAIL = 'user-email';

    private MailQueue $mailer;

    private EmailFactory $emailFactory;

    public function __construct(MailQueue $mailer, EmailFactory $emailFactory)
    {
        $this->mailer = $mailer;
        $this->emailFactory = $emailFactory;
    }

    public function sendInvitationEmail(string $email, string $token, RoleModel $role): EmailService
    {
        $this->mailer->queue(
            $this->emailFactory->createInvitationMail($email, $token, $role),
            self::QUEUE_NAME_USER_EMAIL
        );

        return $this;
    }
}
