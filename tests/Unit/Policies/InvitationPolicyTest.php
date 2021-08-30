<?php

namespace Tests\Unit\Policies;

use App\Policies\InvitationPolicy;
use App\Projects\RoleManager;
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

    private function setUpDeclineTest(bool $isInvitedUser = true, bool $isMemberWithPermission = false): array
    {
        $email = $this->getFaker()->safeEmail;
        $user = $this->createUserModel();
        $this->mockUserModelGetEmail($user, $email);
        $project = $this->createProjectModel();
        $role = $this->createRoleModel();
        $this->mockRoleModelGetProject($role, $project);
        $invitation = $this->createInvitationModel();
        $this->mockInvitationModelGetRole($invitation, $role);
        $this->mockInvitationModelGetEmail($invitation, ($isInvitedUser ? '' : $this->getFaker()->word) . $email);
        $roleManager = $this->createRoleManager();
        $this->mockRoleManagerHasPermissionForAction($roleManager, $isMemberWithPermission, $project, $user, 'projects.invitations.management');
        $invitationPolicy = $this->getInvitationPolicy($roleManager);

        return [$invitationPolicy, $user, $invitation];
    }

    public function testDeclineWithUserIsInvitedUser(): void
    {
        /** @var InvitationPolicy $invitationPolicy */
        [$invitationPolicy, $user, $invitation] = $this->setUpDeclineTest();

        $this->assertTrue($invitationPolicy->decline($user, $invitation));
    }

    public function testDeclineWithUserMemberOfProjectWithPermission(): void
    {
        /** @var InvitationPolicy $invitationPolicy */
        [$invitationPolicy, $user, $invitation] = $this->setUpDeclineTest(false, true);

        $this->assertTrue($invitationPolicy->decline($user, $invitation));
    }

    public function testDeclineWithUserIsNotInvitedUserAndNotMemberWithPermission(): void
    {
        /** @var InvitationPolicy $invitationPolicy */
        [$invitationPolicy, $user, $invitation] = $this->setUpDeclineTest(false);

        $this->assertFalse($invitationPolicy->decline($user, $invitation));
    }

    private function getInvitationPolicy(RoleManager $roleManager = null): InvitationPolicy
    {
        return new InvitationPolicy($roleManager ?: $this->createRoleManager());
    }
}
