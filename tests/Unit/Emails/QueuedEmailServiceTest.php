<?php

namespace Tests\Unit\Emails;

use App\Emails\EmailFactory;
use App\Emails\QueuedEmailService;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Mail\MailQueue;
use Tests\Helper\EmailsHelper;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class QueuedEmailServiceTest
 *
 * @package Tests\Unit\Emails
 */
final class QueuedEmailServiceTest extends TestCase
{
    use EmailsHelper;
    use ModelHelper;
    use ProjectHelper;

    //region Tests

    /**
     * @return array
     */
    private function setUpSendInvitationMailTest(): array
    {
        $email = $this->getFaker()->safeEmail;
        $token = $this->getFaker()->uuid;
        $role = $this->createRoleModel();
        $this->mockUuidModelGetUuid($role, $this->getFaker()->uuid);
        $invitationEmail = $this->createInvitationEmail();
        $emailFactory = $this->createEmailFactory();
        $this->mockEmailFactoryCreateInvitationMail(
            $emailFactory,
            $invitationEmail,
            $email,
            $token,
            $role
        );
        $mailer = $this->createMailQueue();
        $this->mockMailQueueQueue($mailer, $invitationEmail, 'user-email');
        $emailService = $this->getQueuedEmailService($mailer, $emailFactory);

        return [$emailService, $email, $token, $role];
    }

    /**
     * @return void
     */
    public function testSendInvitationEmail(): void
    {
        /** @var QueuedEmailService $emailService */
        [$emailService, $email, $token, $role] = $this->setUpSendInvitationMailTest();

        $this->assertEquals($emailService, $emailService->sendInvitationEmail($email, $token, $role));
    }

    //endregion

    /**
     * @param MailQueue|null        $mailer
     * @param EmailFactoryTest|null $emailFactory
     *
     * @return QueuedEmailService
     */
    private function getQueuedEmailService(MailQueue $mailer = null, EmailFactory $emailFactory = null): QueuedEmailService
    {
        return new QueuedEmailService(
            $mailer ?: $this->createMailQueue(),
            $emailFactory ?: $this->createEmailFactory()
        );
    }
}
