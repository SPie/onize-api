<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\ProjectsController;
use App\Http\Requests\Projects\Create;
use App\Http\Requests\Projects\Invite;
use App\Projects\ProjectManager;
use App\Projects\RoleModel;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Auth\Access\AuthorizationException;
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

    /**
     * @param bool $withProjects
     *
     * @return array
     */
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

    /**
     * @return void
     */
    public function testUsersProjects(): void
    {
        /** @var ProjectsController $projectsController */
        [$projectsController, $authManager, $response] = $this->setUpUsersProjectsTest();

        $this->assertEquals($response, $projectsController->usersProjects($authManager));
    }

    /**
     * @return void
     */
    public function testUsersProjectsWithoutProjects(): void
    {
        /** @var ProjectsController $projectsController */
        [$projectsController, $authManager, $response] = $this->setUpUsersProjectsTest(false);

        $this->assertEquals($response, $projectsController->usersProjects($authManager));
    }

    /**
     * @return array
     */
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

    /**
     * @return void
     */
    public function testShow(): void
    {
        /** @var ProjectsController $projectsController */
        [$projectsController, $project, $response] = $this->setUpShowTest();

        $this->assertEquals($response, $projectsController->show($project));
    }

    /**
     * @return array
     */
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

    /**
     * @return void
     */
    public function testMembers(): void
    {
        /** @var ProjectsController $projectsController */
        [$projectsController, $project, $response] = $this->setUpMembersTest();

        $this->assertEquals($response, $projectsController->members($project));
    }

    /**
     * @return void
     */
    public function testMembersWithoutMembers(): void
    {
        /** @var ProjectsController $projectsController */
        [$projectsController, $project, $response] = $this->setUpMembersTest(false);

        $this->assertEquals($response, $projectsController->members($project));
    }

    /**
     * @return array
     */
    private function setUpInviteTest(): array
    {
        $email = $this->getFaker()->safeEmail;
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $role = $this->createRoleModel();
        $request = $this->createInviteRequest($email, $metaData);
        $invitationData = [$this->getFaker()->word => $this->getFaker()->word];
        $invitation = $this->createInvitationModel();
        $this->mockInvitationModelToArray($invitation, $invitationData);
        $invitationManager = $this->createInvitationManager();
        $this->mockinvitationManagerInviteMember($invitationManager, $invitation, $role, $email, $metaData);
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $response, ['invitation' => $invitationData], 201);
        $projectsController = $this->getProjectsController(null, $responseFactory);

        return [$projectsController, $role, $request, $invitationManager, $response];
    }

    /**
     * @return void
     */
    public function testInvite(): void
    {
        /** @var ProjectsController $projectsController */
        [$projectsController, $role, $request, $invitationManager, $response] = $this->setUpInviteTest();

        $this->assertEquals($response, $projectsController->invite($role, $request, $invitationManager));
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

    /**
     * @param string|null    $email
     * @param array          $metaData
     *
     * @return Invite|MockInterface
     */
    private function createInviteRequest(string $email = null, array $metaData = []): Invite
    {
        return m::spy(Invite::class)
            ->shouldReceive('getEmail')
            ->andReturn($email ?: $this->getFaker()->safeEmail)
            ->getMock()
            ->shouldReceive('getMetaData')
            ->andReturn($metaData)
            ->getMock();
    }
}
