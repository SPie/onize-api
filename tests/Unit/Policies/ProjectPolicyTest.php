<?php

namespace Tests\Unit\Policies;

use App\Policies\ProjectPolicy;
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

    //endregion

    /**
     * @return ProjectPolicy
     */
    private function getProjectPolicy(): ProjectPolicy
    {
        return new ProjectPolicy();
    }
}
