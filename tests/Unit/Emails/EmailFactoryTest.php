<?php

namespace Tests\Unit\Emails;

use App\Emails\EmailFactory;
use App\Emails\Mails\Invitation;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class EmailFactory
 *
 * @package Tests\Unit\Emails
 */
final class EmailFactoryTest extends TestCase
{
    use ProjectHelper;

    //region Tests

    /**
     * @return void
     */
    public function testCreateInvitationEmail(): void
    {
        $email = $this->getFaker()->safeEmail;
        $token = $this->getFaker()->uuid;
        $role = $this->createRoleModel();

        $this->assertEquals(
            (new Invitation($token, $role))->to($email),
            $this->getEmailFactory()->createInvitationMail($email, $token, $role)
        );
    }

    //endregion

    /**
     * @return EmailFactory
     */
    private function getEmailFactory(): EmailFactory
    {
        return new EmailFactory();
    }
}
