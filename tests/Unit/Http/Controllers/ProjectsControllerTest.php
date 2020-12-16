<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\ProjectsController;
use App\Http\Requests\Projects\Create;
use App\Projects\ProjectManager;
use Illuminate\Contracts\Routing\ResponseFactory;
use Mockery as m;
use Mockery\MockInterface;
use Tests\Helper\AuthHelper;
use Tests\Helper\HttpHelper;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class ProjectsControllerTest
 *
 * @package Tests\Unit\Http\Controllers
 */
final class ProjectsControllerTest extends TestCase
{
    use AuthHelper;
    use HttpHelper;
    use ProjectHelper;
    use UsersHelper;

    //region Tests

    /**
     * @return array
     */
    private function setUpCreateTest(): array
    {
        $metaDataElements = [$this->getFaker()->word => $this->getFaker()->word];
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $request = $this->createCreateRequest(null, null, $metaDataElements, $metaData);
        $user = $this->createUserModel();
        $authManager = $this->createAuthManager();
        $this->mockAuthManagerAuthenticatedUser($authManager, $user);
        $projectData = [$this->getFaker()->word => $this->getFaker()->word];
        $project = $this->createProjectModel();
        $this->mockProjectModelToArray($project, $projectData);
        $projectManager = $this->createProjectManager();
        $this->mockProjectManagerCreateProject(
            $projectManager,
            $project,
            $request->getLabel(),
            $request->getDescription(),
            $metaDataElements
        );
        $role = $this->createRoleModel();
        $roleManager = $this->createRoleManager();
        $this->mockRoleManagerCreateOwnerRole($roleManager, $role, $project, $user, $metaData);
        $this->mockProjectModelAddRole($project, $role);
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $response, ['project' => $projectData], 201);
        $projectsController = $this->getProjectsController($projectManager, $responseFactory);

        return [$projectsController, $request, $authManager, $roleManager, $response, $project, $user];
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        /** @var ProjectsController $projectsController */
        [$projectsController, $request, $authManager, $roleManager, $response, $project, $user] = $this->setUpCreateTest();

        $this->assertEquals($response, $projectsController->create($request, $authManager, $roleManager));
        $this->assertRoleManagerCreateOwnerRole($roleManager, $project, $user, $request->getMetaData());
    }

    //endregion

    /**
     * @param ProjectManager|null  $projectManager
     * @param ResponseFactory|null $responseFactory
     *
     * @return ProjectsController
     */
    private function getProjectsController(
        ProjectManager $projectManager = null,
        ResponseFactory $responseFactory = null
    ): ProjectsController {
        return new ProjectsController(
            $projectManager ?: $this->createProjectManager(),
            $responseFactory ?: $this->createResponseFactory()
        );
    }

    /**
     * @param string|null $label
     * @param string|null $description
     * @param array|null  $metaDataElements
     *
     * @param array       $metaData
     *
     * @return Create|MockInterface
     */
    private function createCreateRequest(
        string $label = null,
        string $description = null,
        array $metaDataElements = [],
        array $metaData = []
    ): Create {
        return m::spy(Create::class)
            ->shouldReceive('getLabel')
            ->andReturn($label ?: $this->getFaker()->word)
            ->getMock()
            ->shouldReceive('getDescription')
            ->andReturn($description ?: $this->getFaker()->word)
            ->getMock()
            ->shouldReceive('getMetaDataElements')
            ->andReturn($metaDataElements)
            ->getMock()
            ->shouldReceive('getMetaData')
            ->andReturn($metaData)
            ->getMock();
    }
}
