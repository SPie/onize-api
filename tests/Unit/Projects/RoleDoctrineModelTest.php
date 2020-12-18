<?php

namespace Tests\Unit\Projects;

use App\Projects\ProjectModel;
use App\Projects\RoleDoctrineModel;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class RoleDoctrineModelTest
 *
 * @package Tests\Unit\Projects
 */
final class RoleDoctrineModelTest extends TestCase
{
    use ProjectHelper;

    //region Tests

    /**
     * @return void
     */
    public function testToArray(): void
    {
        $role = $this->getRoleDoctrineModel();

        $this->assertEquals(
            [
                'uuid'  => $role->getUuid(),
                'label' => $role->getLabel(),
                'owner' => false,
            ],
            $role->toArray()
        );
    }

    /**
     * @return void
     */
    public function testToArrayWithProject(): void
    {
        $projectData = [$this->getFaker()->word => $this->getFaker()->word];
        $project = $this->createProjectModel();
        $this->mockProjectModelToArray($project, $projectData);
        $role = $this->getRoleDoctrineModel(null, $project);

        $this->assertEquals(
            [
                'uuid'    => $role->getUuid(),
                'label'   => $role->getLabel(),
                'owner'   => false,
                'project' => $projectData,
            ],
            $role->toArray(true)
        );
    }

    //endregion

    /**
     * @param string|null       $uuid
     * @param ProjectModel|null $project
     * @param string|null       $label
     *
     * @return RoleDoctrineModel
     */
    private function getRoleDoctrineModel(string $uuid = null, ProjectModel $project = null, string $label = null): RoleDoctrineModel
    {
        return new RoleDoctrineModel(
            $uuid ?: $this->getFaker()->uuid,
            $project ?: $this->createProjectModel(),
            $label ?: $this->getFaker()->word
        );
    }
}
