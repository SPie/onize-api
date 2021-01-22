<?php

namespace Tests\Unit\Http\Binders;

use App\Http\Binders\ProjectBinder;
use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\ProjectManager;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class ProjectBinderTest
 *
 * @package Tests\Unit\Http\Binders
 */
final class ProjectBinderTest extends TestCase
{
    use ProjectHelper;

    //region Tests

    /**
     * @param bool $withProject
     *
     * @return array
     */
    private function setUpBindTest(bool $withProject = true): array
    {
        $uuid = $this->getFaker()->uuid;
        $project = $this->createProjectModel();
        $projectManager = $this->createProjectManager();
        $this->mockProjectManagerGetProject($projectManager, $withProject ? $project : new ModelNotFoundException(), $uuid);
        $projectBinder = $this->getProjectBinder($projectManager);

        return [$projectBinder, $uuid, $project];
    }

    /**
     * @return void
     */
    public function testBind(): void
    {
        /** @var ProjectBinder $projectBinder */
        [$projectBinder, $uuid, $project] = $this->setUpBindTest();

        $this->assertEquals($project, $projectBinder->bind($uuid));
    }

    /**
     * @return void
     */
    public function testBindWithoutProject(): void
    {
        /** @var ProjectBinder $projectBinder */
        [$projectBinder, $uuid] = $this->setUpBindTest(false);

        $this->expectException(ModelNotFoundException::class);

        $projectBinder->bind($uuid);
    }

    //endregion

    private function getProjectBinder(ProjectManager $projectManager = null): ProjectBinder
    {
        return new ProjectBinder($projectManager ?: $this->createProjectManager());
    }
}
