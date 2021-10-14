<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\ProjectsController;
use App\Http\Requests\Projects\Create;
use App\Http\Requests\Projects\CreateRole;
use App\Projects\ProjectManager;
use App\Projects\ProjectModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Mockery as m;
use Mockery\MockInterface;
use Tests\Helper\AuthHelper;
use Tests\Helper\HttpHelper;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class ProjectsControllerTest extends TestCase
{
    use AuthHelper;
    use HttpHelper;
    use ProjectHelper;
    use UsersHelper;

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

    /**
     * @return CreateRole|MockInterface
     */
    private function createCreateRoleRequest(
        string $label = null,
        Collection $permissions = null
    ): CreateRole {
        return m::spy(CreateRole::class)
            ->shouldReceive('getLabel')
            ->andReturn($label ?: $this->getFaker()->word)
            ->getMock()
            ->shouldReceive('getPermissions')
            ->andReturn($permissions ?: new ArrayCollection([]))
            ->getMock();
    }

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

    public function testCreate(): void
    {
        /** @var ProjectsController $projectsController */
        [$projectsController, $request, $authManager, $roleManager, $response, $project, $user] = $this->setUpCreateTest();

        $this->assertEquals($response, $projectsController->create($request, $authManager, $roleManager));
        $this->assertRoleManagerCreateOwnerRole($roleManager, $project, $user, $request->getMetaData());
    }

    private function setUpUsersProjectsTest(bool $withProjects = true): array
    {
        $roleData = [$this->getFaker()->word => $this->getFaker()->word];
        $role = $this->createRoleModel();
        $this->mockRoleModelToArray($role, $roleData, true);
        $member = $this->createMemberModel();
        $this->mockMemberModelGetRole($member, $role);
        $user = $this->createUserModel();
        $this->mockUserModelGetMembers($user, new ArrayCollection($withProjects ? [$member] : []));
        $authManager = $this->createAuthManager();
        $this->mockAuthManagerAuthenticatedUser($authManager, $user);
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson(
            $responseFactory,
            $response,
            ['projects' => $withProjects ? [$roleData] : []]
        );
        $projectsController = $this->getProjectsController(null, $responseFactory);

        return [$projectsController, $authManager, $response];
    }

    public function testUsersProjects(): void
    {
        /** @var ProjectsController $projectsController */
        [$projectsController, $authManager, $response] = $this->setUpUsersProjectsTest();

        $this->assertEquals($response, $projectsController->usersProjects($authManager));
    }

    public function testUsersProjectsWithoutProjects(): void
    {
        /** @var ProjectsController $projectsController */
        [$projectsController, $authManager, $response] = $this->setUpUsersProjectsTest(false);

        $this->assertEquals($response, $projectsController->usersProjects($authManager));
    }

    private function setUpShowTest(): array
    {
        $projectData = [$this->getFaker()->word => $this->getFaker()->word];
        $project = $this->createProjectModel();
        $this->mockProjectModelToArray($project, $projectData);
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $response, ['project' => $projectData]);
        $projectsController = $this->getProjectsController(null, $responseFactory);

        return [$projectsController, $project, $response];
    }

    public function testShow(): void
    {
        /** @var ProjectsController $projectsController */
        [$projectsController, $project, $response] = $this->setUpShowTest();

        $this->assertEquals($response, $projectsController->show($project));
    }

    private function setUpMembersTest(bool $withMembers = true): array
    {
        $userData = [$this->getFaker()->word => $this->getFaker()->word];
        $user = $this->createUserModel();
        $this->mockUserModelToArray($user, $userData);
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $member = $this->createMemberModel();
        $this
            ->mockMemberModelGetUser($member, $user)
            ->mockMemberModelGetMetaData($member, $metaData);
        $project = $this->createProjectModel();
        $this->mockProjectModelGetMembers($project, new ArrayCollection($withMembers ? [$member] : []));
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson(
            $responseFactory,
            $response,
            ['members' => $withMembers ? [\array_merge($userData, ['metaData' => $metaData])] : []]
        );
        $projectsController = $this->getProjectsController(null, $responseFactory);

        return [$projectsController, $project, $response];
    }

    public function testMembers(): void
    {
        /** @var ProjectsController $projectsController */
        [$projectsController, $project, $response] = $this->setUpMembersTest();

        $this->assertEquals($response, $projectsController->members($project));
    }

    public function testMembersWithoutMembers(): void
    {
        /** @var ProjectsController $projectsController */
        [$projectsController, $project, $response] = $this->setUpMembersTest(false);

        $this->assertEquals($response, $projectsController->members($project));
    }

    public function testRemoveMember(): void
    {
        $project = $this->createProjectModel();
        $user = $this->createUserModel();
        $projectManager = $this->createProjectManager();
        $jsonResponse = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $jsonResponse, [], 204);

        $this->assertEquals($jsonResponse, $this->getProjectsController($projectManager, $responseFactory)->removeMember($project, $user));
        $this->assertProjectManagerRemoveMember($projectManager, $project, $user);
    }

    public function testCreateRole(): void
    {
        $project = $this->createProjectModel();
        $label = $this->getFaker()->word;
        $permissions = new ArrayCollection([$this->createPermissionModel()]);
        $request = $this->createCreateRoleRequest($label, $permissions);
        $roleData = [$this->getFaker()->word => $this->getFaker()->word];
        $role = $this->createRoleModel();
        $this->mockRoleModelToArray($role, $roleData);
        $roleManager = $this->createRoleManager();
        $this->mockRoleManagerCreateRole($roleManager, $role, $project, $label, $permissions);
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $response, ['role' => $roleData], 201);

        $this->assertEquals(
            $response,
            $this->getProjectsController(null, $responseFactory)->createRole($project, $request, $roleManager)
        );
    }

    private function setUpChangeRoleTest(bool $withAuthorizedUser = true): array
    {
        $project = $this->createProjectModel();
        $user = $this->createUserModel();
        $role = $this->createRoleModel();
        $request = $this->createChangeRoleRequest($user, $role);
        $gate = $this->createGate();
        $this->mockGateAuthorize(
            $gate,
            $withAuthorizedUser ? $this->createJsonResponse() : new AuthorizationException(),
            'changeRole',
            [$project, $user]
        );
        $projectManager = $this->createProjectManager();
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $response, [], 204);
        $projectsController = $this->getProjectsController($projectManager, $responseFactory);

        return [$projectsController, $project, $request, $gate, $response, $projectManager, $user, $role];
    }

    public function testChangeRole(): void
    {
        [$projectsController, $project, $request, $gate, $response, $projectManager, $user, $role] = $this->setUpChangeRoleTest();

        $this->assertEquals($response, $projectsController->changeRole($project, $request, $gate));
        $this->assertProjectManagerChangeRole($projectManager, $user, $role);
    }

    public function testChangeRoleWithoutAuthorizedUser(): void
    {
        [$projectsController, $project, $request, $gate] = $this->setUpChangeRoleTest(withAuthorizedUser: false);

        $this->expectException(AuthorizationException::class);

        $projectsController->changeRole($project, $request, $gate);
    }
}
