<?php

namespace Tests\Unit\Projects;

use App\Projects\MetaDataElementModelFactory;
use App\Projects\MetaDataElementRepository;
use App\Projects\ProjectManager;
use App\Projects\ProjectModelFactory;
use App\Projects\ProjectRepository;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class ProjectManagerTest
 *
 * @package Tests\Unit\Projects
 */
final class ProjectManagerTest extends TestCase
{
    use ProjectHelper;
    use ModelHelper;

    //region Tests

    /**
     * @param bool $withOptionalMetaDataElementProperties
     *
     * @return array
     */
    private function setUpCreateProjectTest(bool $withOptionalMetaDataElementProperties = true): array
    {
        $name = $this->getFaker()->word;
        $description = $this->getFaker()->word;
        $metaDataElement = [
            'name'     => $this->getFaker()->word,
            'label'    => $this->getFaker()->word,
            'type'     => $this->createRandomMetaDataElementType(),
        ];
        if ($withOptionalMetaDataElementProperties) {
            $metaDataElement['required'] = $this->getFaker()->boolean;
            $metaDataElement['inList'] = $this->getFaker()->boolean;
        }
        $metaDataElements = [$metaDataElement];
        $project = $this->createProjectModel();
        $projectModelFactory = $this->createProjectModelFactory();
        $this->mockProjectModelFactoryCreate($projectModelFactory, $project, $name, $description);
        $savedProject = $this->createProjectModel();
        $projectRepository = $this->createProjectRepository();
        $this->mockRepositorySave($projectRepository, $project, null, $savedProject);
        $metaDataElementModel = $this->createMetaDataElementModel();
        $metaDataElementModelFactory = $this->createMetaDataElementModelFactory();
        $this->mockMetaDataElementModelFactoryCreate(
            $metaDataElementModelFactory,
            $metaDataElementModel,
            $savedProject,
            $metaDataElements[0]['name'],
            $metaDataElements[0]['label'],
            $metaDataElements[0]['type'],
            $withOptionalMetaDataElementProperties ? $metaDataElements[0]['required'] : false,
            $withOptionalMetaDataElementProperties ? $metaDataElements[0]['inList'] : false,
        );
        $metaDataElementRepository = $this->createMetaDataElementRepository();
        $this->mockRepositorySave($metaDataElementRepository, $metaDataElementModel);
        $projectManager = $this->getProjectManager(
            $projectRepository,
            $projectModelFactory,
            $metaDataElementRepository,
            $metaDataElementModelFactory
        );

        return [
            $projectManager,
            $name,
            $description,
            $metaDataElements,
            $savedProject,
            $metaDataElementRepository,
            $metaDataElementModel,
        ];
    }

    /**
     * @return void
     */
    public function testCreateProject(): void
    {
        /** @var ProjectManager $projectManager */
        [
            $projectManager,
            $name,
            $description,
            $metaDataElements,
            $project,
            $metaDataElementRepository,
            $metaDataElementModel,
        ] = $this->setUpCreateProjectTest();

        $this->assertEquals($project, $projectManager->createProject($name, $description, $metaDataElements));
        $this->assertRepositorySave($metaDataElementRepository, $metaDataElementModel);
    }

    /**
     * @return void
     */
    public function testCreateProjectWithoutOptionalMetaDataElementProperties(): void
    {
        /** @var ProjectManager $projectManager */
        [
            $projectManager,
            $name,
            $description,
            $metaDataElements,
            $project,
            $metaDataElementRepository,
            $metaDataElementModel,
        ] = $this->setUpCreateProjectTest(false);

        $this->assertEquals($project, $projectManager->createProject($name, $description, $metaDataElements));
        $this->assertRepositorySave($metaDataElementRepository, $metaDataElementModel);
    }

    //endregion

    /**
     * @param ProjectRepository|null           $projectRepository
     * @param ProjectModelFactory|null         $projectModelFactory
     * @param MetaDataElementRepository|null   $metaDataElementRepository
     * @param MetaDataElementModelFactory|null $metaDataElementModelFactory
     *
     * @return ProjectManager
     */
    private function getProjectManager(
        ProjectRepository $projectRepository = null,
        ProjectModelFactory $projectModelFactory = null,
        MetaDataElementRepository $metaDataElementRepository = null,
        MetaDataElementModelFactory $metaDataElementModelFactory = null
    ): ProjectManager {
        return new ProjectManager(
            $projectRepository ?: $this->createProjectRepository(),
            $projectModelFactory ?: $this->createProjectModelFactory(),
            $metaDataElementRepository ?: $this->createMetaDataElementRepository(),
            $metaDataElementModelFactory ?: $this->createMetaDataElementModelFactory()
        );
    }
}
