<?php

namespace Tests\Unit\Policies;

use App\Policies\RolePolicy;
use App\Projects\PermissionModel;
use App\Projects\RoleManager;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class RolePolicyTest extends TestCase
{
    use ProjectHelper;
    use UsersHelper;

    private function getRolePolicy(RoleManager $roleManager = null): RolePolicy
    {
        return new RolePolicy($roleManager ?: $this->createRoleManager());
    }

    private function setUpInviteTest(bool $allowed = true): array
    {
        $user = $this->createUserModel();
        $project = $this->createProjectModel();
        $role = $this->createRoleModel();
        $this->mockRoleModelGetProject($role, $project);
        $roleManager = $this->createRoleManager();
        $this->mockRoleManagerHasPermissionForAction(
            $roleManager,
            $allowed,
            $project,
            $user,
            PermissionModel::PERMISSION_PROJECTS_INVITATIONS_MANAGEMENT
        );
        $rolePolicy = $this->getRolePolicy($roleManager);

        return [$rolePolicy, $user, $role];
    }

    public function testInvite(): void
    {
        /** @var RolePolicy $rolePolicy */
        [$rolePolicy, $user, $role] = $this->setUpInviteTest();

        $this->assertTrue($rolePolicy->invite($user, $role));
    }

    public function testInviteWithourPermission(): void
    {
        /** @var RolePolicy $rolePolicy */
        [$rolePolicy, $user, $role] = $this->setUpInviteTest(false);

        $this->assertFalse($rolePolicy->invite($user, $role));
    }

    private function setUpRemoveRoleTest(bool $allowed = true, bool $isOwnerRole = false): array
    {
        $user = $this->createUserModel();
        $project = $this->createProjectModel();
        $role = $this->createRoleModel();
        $this
            ->mockRoleModelGetProject($role, $project)
            ->mockRoleModelIsOwner($role, $isOwnerRole);
        $roleManager = $this->createRoleManager();
        $this->mockRoleManagerHasPermissionForAction(
            $roleManager,
            $allowed,
            $project,
            $user,
            PermissionModel::PERMISSION_PROJECTS_ROLES_MANAGEMENT
        );
        $rolePolicy = $this->getRolePolicy($roleManager);

        return [$rolePolicy, $user, $role];
    }

    public function testRemoveRole(): void
    {
        /** @var RolePolicy $rolePolicy */
        [$rolePolicy, $user, $role] = $this->setUpRemoveRoleTest();

        $this->assertTrue($rolePolicy->removeRole($user, $role));
    }

    public function testRemoveRoleWithoutPermission(): void
    {
        /** @var RolePolicy $rolePolicy */
        [$rolePolicy, $user, $role] = $this->setUpRemoveRoleTest(allowed: false);

        $this->assertFalse($rolePolicy->removeRole($user, $role));
    }

    public function testRemoveRoleWithOwnerRole(): void
    {
        /** @var RolePolicy $rolePolicy */
        [$rolePolicy, $user, $role] = $this->setUpRemoveRoleTest(isOwnerRole: true);

        $this->assertFalse($rolePolicy->removeRole($user, $role));
    }
}
