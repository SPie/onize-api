<?php

namespace Tests\Unit\Http\Rules;

use App\Http\Rules\ProjectExists;
use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\ProjectManager;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

final class ProjectExistsTest extends TestCase
{
    use ProjectHelper;

    private function getProjectExistsRule(ProjectManager $projectManager = null): ProjectExists
    {
        return new ProjectExists($projectManager ?: $this->createProjectManager());
    }

    public function testMessage(): void
    {
        $this->assertEquals('validation.project-not-found', $this->getProjectExistsRule()->message());
    }

    private function setUpPassesTest(bool $withProject = true): array
    {
        $uuid = $this->getFaker()->uuid;
        $project = $this->createProjectModel();
        $projectManager = $this->createProjectManager();
        $this->mockProjectManagerGetProject($projectManager, $withProject ? $project : new ModelNotFoundException(), $uuid);
        $rule = $this->getProjectExistsRule($projectManager);

        return [$rule, $uuid, $project];
    }

    public function testPasses(): void
    {
        /** @var ProjectExists $rule */
        [$rule, $uuid, $project] = $this->setUpPassesTest();

        $this->assertTrue($rule->passes($this->getFaker()->word, $uuid));
        $this->assertEquals($project, $rule->getProject());
    }

    public function testPassesWithoutProject(): void
    {
        /** @var ProjectExists $rule */
        [$rule, $uuid] = $this->setUpPassesTest(false);

        $this->assertFalse($rule->passes($this->getFaker()->word, $uuid));
    }
}
