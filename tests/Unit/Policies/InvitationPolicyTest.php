<?php

namespace Tests\Unit\Policies;

use App\Policies\InvitationPolicy;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class InvitationPolicyTest extends TestCase
{
    use ProjectHelper;
    use UsersHelper;

    private function setUpAcceptTest(bool $alreadyMember = false, bool $matchingEmails = true): array
    {
        $email = $this->getFaker()->safeEmail;
        $project = $this->createProjectModel();
        $role = $this->createRoleModel();
        $this->mockRoleModelGetProject($role, $project);
        $invitation = $this->createInvitationModel();
        $this
            ->mockInvitationModelGetRole($invitation, $role)
            ->mockInvitationModelGetEmail($invitation, $email);
        $user = $this->createUserModel();
        $this
            ->mockUserModelGetEmail($user, ($matchingEmails ? '' : $this->getFaker()->word) . $email)
            ->mockUserModelIsMemberOfProject($user, $alreadyMember, $project);
        $invitationPolicy = $this->getInvitationPolicy();

        return [$invitationPolicy, $user, $invitation];
    }

    public function testAcceptAllowed(): void
    {
        /** @var InvitationPolicy $invitationPolicy */
        [$invitationPolicy, $user, $invitation] = $this->setUpAcceptTest();

        $this->assertTrue($invitationPolicy->accept($user, $invitation));
    }

    public function testAcceptWithAlreadyMember(): void
    {
        /** @var InvitationPolicy $invitationPolicy */
        [$invitationPolicy, $user, $invitation] = $this->setUpAcceptTest(true);

        $this->assertFalse($invitationPolicy->accept($user, $invitation));
    }

    public function testAcceptWithoutMatchingEmail(): void
    {
        /** @var InvitationPolicy $invitationPolicy */
        [$invitationPolicy, $user, $invitation] = $this->setUpAcceptTest(false, false);

        $this->assertFalse($invitationPolicy->accept($user, $invitation));
    }

    private function getInvitationPolicy(): InvitationPolicy
    {
        return new InvitationPolicy();
    }
}
