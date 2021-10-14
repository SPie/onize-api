<?php

namespace Tests\Unit\Policies;

use App\Policies\ProjectPolicy;
use App\Projects\PermissionModel;
use App\Projects\RoleManager;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class ProjectPolicyTest extends TestCase
{
    use ProjectHelper;
    use UsersHelper;

    private function getProjectPolicy(RoleManager $roleManager = null): ProjectPolicy
    {
        return new ProjectPolicy($roleManager ?: $this->createRoleManager());
    }

    private function setUpShowTest(bool $allowed = true): array
    {
        $project = $this->createProjectModel();
        $user = $this->createUserModel();
        $this->mockUserModelIsMemberOfProject($user, $allowed, $project);
        $projectPolicy = $this->getProjectPolicy();

        return [$projectPolicy, $user, $project];
    }

    public function testShowForMember(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project] = $this->setUpShowTest();

        $this->assertTrue($projectPolicy->show($user, $project));
    }

    public function testShowForGuest(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project] = $this->setUpShowTest(false);

        $this->assertFalse($projectPolicy->show($user, $project));
    }

    private function setUpMembersTest(bool $allowed = true): array
    {
        $user = $this->createUserModel();
        $project = $this->createProjectModel();
        $roleManager = $this->createRoleManager();
        $this->mockRoleManagerHasPermissionForAction(
            $roleManager,
            $allowed,
            $project,
            $user,
            PermissionModel::PERMISSION_PROJECTS_MEMBERS_SHOW
        );
        $projectPolicy = $this->getProjectPolicy($roleManager);

        return [$projectPolicy, $user, $project];
    }

    public function testMembers(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project] = $this->setUpMembersTest();

        $this->assertTrue($projectPolicy->members($user, $project));
    }

    public function testMembersWithoutPermission(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project] = $this->setUpMembersTest(false);

        $this->assertFalse($projectPolicy->members($user, $project));
    }

    private function setUpRemoveMemberTest(
        bool $allowed = true,
        bool $memberIsOwner = false,
        bool $authenticatedUserIsOwner = false,
        bool $userIsMember = true
    ): array {
        $role = $this->createRoleModel();
        $this->mockRoleModelIsOwner($role, $authenticatedUserIsOwner);
        $project = $this->createProjectModel();
        $user = $this->createUserModel();
        $this->mockUserModelGetRoleForProject($user, $role, $project);
        $memberRole = $this->createRoleModel();
        $this->mockRoleModelIsOwner($memberRole, $memberIsOwner);
        $memberUser = $this->createUserModel();
        $this->mockUserModelGetRoleForProject($memberUser, $userIsMember ? $memberRole : null, $project);
        $roleManager = $this->createRoleManager();
        $this->mockRoleManagerHasPermissionForAction(
            $roleManager,
            $allowed,
            $project,
            $user,
            PermissionModel::PERMISSION_PROJECTS_MEMBER_MANAGEMENT
        );
        $projectPolicy = $this->getProjectPolicy($roleManager);

        return [$projectPolicy, $user, $project, $memberUser];
    }

    public function testRemoveMember(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project, $member] = $this->setUpRemoveMemberTest();

        $this->assertTrue($projectPolicy->removeMember($user, $project, $member));
    }

    public function testRemoveMembersWithoutPermission(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project, $member] = $this->setUpRemoveMemberTest(allowed: false);

        $this->assertFalse($projectPolicy->removeMember($user, $project, $member));
    }

    public function testRemoveMemberWithRemovingOwner(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project, $member] = $this->setUpRemoveMemberTest(memberIsOwner: true);

        $this->assertFalse($projectPolicy->removeMember($user, $project, $member));
    }

    public function testRemoveMemberWithRemovingOwnerAndAuthenticatedUserIsOwner(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project, $member] = $this->setUpRemoveMemberTest(memberIsOwner: true, authenticatedUserIsOwner: true);

        $this->assertTrue($projectPolicy->removeMember($user, $project, $member));
    }

    public function testRemoveMemberWithUserNotMemberOfProject(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project, $member] = $this->setUpRemoveMemberTest(userIsMember: false);

        $this->assertTrue($projectPolicy->removeMember($user, $project, $member));
    }

    private function setUpInviteTest(bool $allowed = true): array
    {
        $user = $this->createUserModel();
        $project = $this->createProjectModel();
        $roleManager = $this->createRoleManager();
        $this->mockRoleManagerHasPermissionForAction(
            $roleManager,
            $allowed,
            $project,
            $user,
            PermissionModel::PERMISSION_PROJECTS_INVITATIONS_MANAGEMENT
        );
        $projectPolicy = $this->getProjectPolicy($roleManager);

        return [$projectPolicy, $user, $project];
    }

    public function testInvite(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project] = $this->setUpInviteTest();

        $this->assertTrue($projectPolicy->invite($user, $project));
    }

    public function testInviteWithoutPermission(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project] = $this->setUpInviteTest(allowed: false);

        $this->assertFalse($projectPolicy->invite($user, $project));
    }

    private function setUpCreateRoleTest(bool $allowed = true): array
    {
        $user = $this->createUserModel();
        $project = $this->createProjectModel();
        $roleManager = $this->createRoleManager();
        $this->mockRoleManagerHasPermissionForAction(
            $roleManager,
            $allowed,
            $project,
            $user,
            PermissionModel::PERMISSION_PROJECTS_ROLES_MANAGEMENT
        );
        $projectPolicy = $this->getProjectPolicy($roleManager);

        return [$projectPolicy, $user, $project];
    }

    public function testCreateRole(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project] = $this->setUpCreateRoleTest();

        $this->assertTrue($projectPolicy->createRole($user, $project));
    }

    public function testCreateRoleWithoutPermission(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project] = $this->setUpCreateRoleTest(false);

        $this->assertFalse($projectPolicy->createRole($user, $project));
    }

    private function setUpChangeRoleTest(
        bool $allowed = true,
        bool $memberIsOwner = false,
        bool $userIsOwner = false
    ): array {
        $project = $this->createProjectModel();
        $role = $this->createRoleModel();
        $this->mockRoleModelIsOwner($role, $userIsOwner);
        $userMember = $this->createMemberModel();
        $this->mockMemberModelGetRole($userMember, $role);
        $user = $this->createUserModel();
        $this->mockUserModelGetMemberOfProject($user, $userMember, $project);
        $memberRole = $this->createRoleModel();
        $this->mockRoleModelIsOwner($memberRole, $memberIsOwner);
        $member = $this->createMemberModel();
        $this->mockMemberModelGetRole($member, $memberRole);
        $memberUser = $this->createUserModel();
        $this->mockUserModelGetMemberOfProject($memberUser, $member, $project);
        $roleManager = $this->createRoleManager();
        $this->mockRoleManagerHasPermissionForAction(
            $roleManager,
            $allowed,
            $project,
            $user,
            PermissionModel::PERMISSION_PROJECTS_ROLES_MANAGEMENT
        );
        $projectPolicy = $this->getProjectPolicy($roleManager);

        return [$projectPolicy, $user, $project, $memberUser];
    }

    public function testChangeRole(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project, $memberUser] = $this->setUpChangeRoleTest();

        $this->assertTrue($projectPolicy->changeRole($user, $project, $memberUser));
    }

    public function testChangeRoleWithoutPermission(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project, $memberUser] = $this->setUpChangeRoleTest(allowed: false);

        $this->assertFalse($projectPolicy->changeRole($user, $project, $memberUser));
    }

    public function testChangeRoleWithOwnerUser(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project, $memberUser] = $this->setUpChangeRoleTest(memberIsOwner: true);

        $this->assertFalse($projectPolicy->changeRole($user, $project, $memberUser));
    }

    public function testChangeRoleWithOwnerUserAndAuthenticatedUserIsOwner(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project, $memberUser] = $this->setUpChangeRoleTest(memberIsOwner: true, userIsOwner: true);

        $this->assertTrue($projectPolicy->changeRole($user, $project, $memberUser));
    }
}
