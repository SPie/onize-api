<?php

namespace Tests\Unit\Projects;

use App\Projects\ProjectDoctrineModel;
use App\Users\UserDoctrineModel;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class ProjectDoctrineModelTest
 *
 * @package Tests\Unit\Projects
 */
final class ProjectDoctrineModelTest extends TestCase
{
    use ProjectHelper;

    //region Tests

    /**
     * @return void
     */
    public function testToArray(): void
    {
        $project = $this->getProjectDoctrineModel();

        $this->assertEquals(
            [
                'uuid'             => $project->getUuid(),
                'label'            => $project->getLabel(),
                'description'      => $project->getDescription(),
                'metaDataElements' => [],
                'roles'            => [],
            ],
            $project->toArray()
        );
    }

    /**
     * @return void
     */
    public function testToArrayWithMetaDataElementsAndRoles(): void
    {
        $metaDataElementModelData = [$this->getFaker()->word => $this->getFaker()];
        $metaDataElement = $this->createMetaDataElementModel();
        $this->mockMetaDataElementModelToArray($metaDataElement, $metaDataElementModelData);
        $roleData = [$this->getFaker()->word => $this->getFaker()->word];
        $role = $this->createRoleModel();
        $this->mockRoleModelToArray($role, $roleData);
        $project = $this->getProjectDoctrineModel()
            ->addMetaDataElement($metaDataElement)
            ->addRole($role);

        $this->assertEquals(
            [
                'uuid'             => $project->getUuid(),
                'label'            => $project->getLabel(),
                'description'      => $project->getDescription(),
                'metaDataElements' => [$metaDataElementModelData],
                'roles'            => [$roleData],
            ],
            $project->toArray()
        );
    }

    //endregion

    /**
     * @param string|null $uuid
     * @param string|null $label
     * @param string|null $description
     *
     * @return ProjectDoctrineModel
     */
    private function getProjectDoctrineModel(
        string $uuid = null,
        string $label = null,
        string $description = null
    ): ProjectDoctrineModel {
        return new ProjectDoctrineModel(
            $uuid ?: $this->getFaker()->uuid,
            $label ?: $this->getFaker()->word,
            $description ?: $this->getFaker()->word
        );
    }
}
