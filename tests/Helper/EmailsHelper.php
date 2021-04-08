<?php

namespace Tests\Helper;

use App\Emails\EmailFactory;
use App\Emails\EmailService;
use App\Emails\Mails\Invitation;
use App\Projects\RoleModel;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Mail\MailQueue;
use Mockery as m;
use Mockery\MockInterface;

/**
 * Trait EmailsHelper
 *
 * @package Tests\Helper
 */
trait EmailsHelper
{
    /**
     * @return EmailService|MockInterface
     */
    private function createEmailService(): EmailService
    {
        return m::spy(EmailService::class);
    }

    /**
     * @param EmailService|MockInterface $emailService
     * @param string                     $email
     * @param string                     $token
     * @param RoleModel                  $role
     * @param \Exception|null            $exception
     *
     * @return $this
     */
    private function mockEmailServiceSendInvitationEmail(
        MockInterface $emailService,
        string $email,
        string $token,
        RoleModel $role,
        \Exception $exception = null
    ): self {
        $emailService
            ->shouldReceive('sendInvitationEmail')
            ->with($email, $token, $role)
            ->andThrow($exception ?: $emailService)
            ->once();

        return $this;
    }

    /**
     * @return MailQueue|MockInterface
     */
    private function createMailQueue(): MailQueue
    {
        return m::spy(MailQueue::class);
    }

    /**
     * @param MockInterface $mailQueue
     * @param Mailable      $mail
     * @param string        $queue
     *
     * @return $this
     */
    private function mockMailQueueQueue(MockInterface $mailQueue, Mailable $mail, string $queue): self
    {
        $mailQueue
            ->shouldReceive('queue')
            ->with($mail, $queue)
            ->once();

        return $this;
    }

    /**
     * @return EmailFactory|MockInterface
     */
    private function createEmailFactory(): EmailFactory
    {
        return m::spy(EmailFactory::class);
    }

    /**
     * @param EmailFactory|MockInterface $emailFactory
     * @param Invitation                 $invitationMail
     * @param string                     $email
     * @param string                     $token
     * @param RoleModel                  $role
     *
     * @return $this
     */
    private function mockEmailFactoryCreateInvitationMail(
        MockInterface $emailFactory,
        Invitation $invitationMail,
        string $email,
        string $token,
        RoleModel $role
    ): self {
        $emailFactory
            ->shouldReceive('createInvitationMail')
            ->with($email, $token, $role)
            ->andReturn($invitationMail);

        return $this;
    }

    /**
     * @return Invitation|MockInterface
     */
    private function createInvitationEmail(): Invitation
    {
        return m::spy(Invitation::class);
    }
}
