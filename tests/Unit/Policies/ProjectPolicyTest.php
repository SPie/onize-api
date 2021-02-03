<?php

namespace Tests\Unit\Policies;

use App\Policies\ProjectPolicy;
use App\Projects\PermissionModel;
use App\Projects\RoleManager;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class ProjectPolicyTest
 *
 * @package Tests\Unit\Policies
 */
final class ProjectPolicyTest extends TestCase
{
    use ProjectHelper;
    use UsersHelper;

    //region Tests

    /**
     * @return array
     */
    private function setUpShowTest(bool $allowed = true): array
    {
        $project = $this->createProjectModel();
        $user = $this->createUserModel();
        $this->mockUserModelIsMemberOfProject($user, $allowed, $project);
        $projectPolicy = $this->getProjectPolicy();

        return [$projectPolicy, $user, $project];
    }

    /**
     * @return void
     */
    public function testShowForMember(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project] = $this->setUpShowTest();

        $this->assertTrue($projectPolicy->show($user, $project));
    }

    /**
     * @return void
     */
    public function testShowForGuest(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project] = $this->setUpShowTest(false);

        $this->assertFalse($projectPolicy->show($user, $project));
    }

    /**
     * @param bool $allowed
     *
     * @return array
     */
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

    /**
     * @return void
     */
    public function testMembers(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project] = $this->setUpMembersTest();

        $this->assertTrue($projectPolicy->members($user, $project));
    }

    /**
     * @return void
     */
    public function testMembersWithoutPermission(): void
    {
        /** @var ProjectPolicy $projectPolicy */
        [$projectPolicy, $user, $project] = $this->setUpMembersTest(false);

        $this->assertFalse($projectPolicy->members($user, $project));
    }

    //endregion

    /**
     * @param RoleManager|null $roleManager
     *
     * @return ProjectPolicy
     */
    private function getProjectPolicy(RoleManager $roleManager = null): ProjectPolicy
    {
        return new ProjectPolicy($roleManager ?: $this->createRoleManager());
    }
}
