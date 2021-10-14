<?php

namespace Tests\Unit\Http\Rules;

use App\Http\Rules\RoleExists;
use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\RoleManager;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

final class RoleExistsTest extends TestCase
{
    use ModelHelper;
    use ProjectHelper;

    private function getRoleExists(RoleManager $roleManager = null): RoleExists
    {
        return new RoleExists($roleManager ?: $this->createRoleManager());
    }

    private function setUpPassesTest(bool $withRole = true, bool $withProject = false, bool $matchingProjects = true): array
    {
        $uuid = $this->getFaker()->uuid;
        $project = $this->createProjectModel();
        $this->mockModelGetId($project, $this->getFaker()->numberBetween(1));
        $otherProject = $this->createProjectModel();
        $this->mockModelGetId($otherProject, $project->getId() + 1);
        $role = $this->createRoleModel();
        $this->mockRoleModelGetProject($role, $project);
        $roleManager = $this->createRoleManager();
        $this->mockRoleManagerGetRole($roleManager, $withRole ? $role : new ModelNotFoundException(), $uuid);
        $rule = $this->getRoleExists($roleManager);
        if ($withProject) {
            $rule->setProject($matchingProjects ? $project : $otherProject);
        }

        return [$rule, $uuid, $role];
    }

    public function testPassesWithRole(): void
    {
        /** @var RoleExists $rule */
        [$rule, $uuid, $role] = $this->setUpPassesTest();

        $this->assertTrue($rule->passes($this->getFaker()->word, $uuid));
        $this->assertEquals($role, $rule->getRole());
    }

    public function testPassesWithoutRole(): void
    {
        /** @var RoleExists $rule */
        [$rule, $uuid] = $this->setUpPassesTest(false);

        $this->assertFalse($rule->passes($this->getFaker()->word, $uuid));
    }

    public function testPassesWithRoleAndMatchingProject(): void
    {
        /** @var RoleExists $rule */
        [$rule, $uuid, $role] = $this->setUpPassesTest(withProject: true);

        $this->assertTrue($rule->passes($this->getFaker()->word, $uuid));
        $this->assertEquals($role, $rule->getRole());
    }

    public function testPassesWithRoleAndNotMatchingProject(): void
    {
        /** @var RoleExists $rule */
        [$rule, $uuid] = $this->setUpPassesTest(withProject: true, matchingProjects: false);

        $this->assertFalse($rule->passes($this->getFaker()->word, $uuid));
        $this->assertNull($rule->getRole());
    }

    public function testMessage(): void
    {
        $this->assertEquals('validation.role-not-found', $this->getRoleExists()->message());
    }
}
