<?php

namespace Tests\Unit\Policies;

use App\Policies\RolePolicy;
use App\Projects\PermissionModel;
use App\Projects\RoleManager;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class RolePolicyTest
 *
 * @package Tests\Unit\Policies
 */
final class RolePolicyTest extends TestCase
{
    use ProjectHelper;
    use UsersHelper;

    //region Tests

    /**
     * @return array
     */
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

    /**
     * @return void
     */
    public function testInvite(): void
    {
        /** @var RolePolicy $rolePolicy */
        [$rolePolicy, $user, $role] = $this->setUpInviteTest();

        $this->assertTrue($rolePolicy->invite($user, $role));
    }

    /**
     * @return void
     */
    public function testInviteWithourPermission(): void
    {
        /** @var RolePolicy $rolePolicy */
        [$rolePolicy, $user, $role] = $this->setUpInviteTest(false);

        $this->assertFalse($rolePolicy->invite($user, $role));
    }

    //endregion

    /**
     * @param RoleManager|null $roleManager
     *
     * @return RolePolicy
     */
    private function getRolePolicy(RoleManager $roleManager = null): RolePolicy
    {
        return new RolePolicy($roleManager ?: $this->createRoleManager());
    }
}
