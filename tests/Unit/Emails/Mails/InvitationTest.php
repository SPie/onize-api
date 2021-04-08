<?php

namespace Tests\Unit\Emails\Mails;

use App\Emails\Mails\Invitation;
use App\Projects\RoleModel;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class InvitationTest
 *
 * @package Tests\Unit\Emails\Mails
 */
final class InvitationTest extends TestCase
{
    use ProjectHelper;

    //region Tests

    /**
     * @return void
     */
    public function testDataOnCreation(): void
    {
        $token = $this->getFaker()->uuid;
        $role = $this->createRoleModel();

        $invitation = $this->getInvitation($token, $role);

        $this->assertEquals('Project Invitation', $invitation->subject);
        $this->assertEquals('emails.projects.invitation', $invitation->view);
        $this->assertEquals(['token' => $token, 'role' => $role], $invitation->viewData);
    }
    
    //endregion

    /**
     * @param string|null    $token
     * @param RoleModel|null $role
     *
     * @return Invitation
     */
    private function getInvitation(string $token = null, RoleModel $role = null): Invitation
    {
        return new Invitation(
            $token ?: $this->getFaker()->uuid,
            $role ?: $this->createRoleModel()
        );
    }
}
