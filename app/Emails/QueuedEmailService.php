<?php

namespace App\Emails;

use App\Projects\RoleModel;
use Illuminate\Contracts\Mail\MailQueue;

/**
 * Class QueuedEmailService
 *
 * @package App\Emails
 */
final class QueuedEmailService implements EmailService
{
    private const QUEUE_NAME_USER_EMAIL = 'user-email';

    /**
     * @var MailQueue
     */
    private MailQueue $mailer;

    /**
     * @var EmailFactory
     */
    private EmailFactory $emailFactory;

    /**
     * QueuedEmailService constructor.
     *
     * @param MailQueue    $mailer
     * @param EmailFactory $emailFactory
     */
    public function __construct(MailQueue $mailer, EmailFactory $emailFactory)
    {
        $this->mailer = $mailer;
        $this->emailFactory = $emailFactory;
    }

    /**
     * @param string    $email
     * @param string    $token
     * @param RoleModel $role
     *
     * @return EmailService
     */
    public function sendInvitationEmail(string $email, string $token, RoleModel $role): EmailService
    {
        $this->mailer->queue(
            $this->emailFactory->createInvitationMail($email, $token, $role),
            self::QUEUE_NAME_USER_EMAIL
        );

        return $this;
    }
}
