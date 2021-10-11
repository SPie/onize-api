<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\InvitationsController;
use App\Http\Requests\Projects\AcceptInvitation;
use App\Http\Requests\Projects\Invite;
use App\Projects\Invites\Exceptions\RoleProjectNotAllowedException;
use App\Projects\Invites\InvitationManager;
use App\Projects\RoleModel;
use Illuminate\Contracts\Routing\ResponseFactory;
use Mockery as m;
use Mockery\MockInterface;
use Tests\Helper\AuthHelper;
use Tests\Helper\HttpHelper;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class InvitationsControllerTest extends TestCase
{
    use AuthHelper;
    use HttpHelper;
    use ModelHelper;
    use ProjectHelper;
    use UsersHelper;

    private function getInvitationsController(
        InvitationManager $invitationManager = null,
        ResponseFactory $responseFactory = null
    ): InvitationsController {
        return new InvitationsController(
            $invitationManager ?: $this->createInvitationManager(),
            $responseFactory ?: $this->createResponseFactory()
        );
    }

    /**
     * @return Invite|MockInterface
     */
    private function createInviteRequest(RoleModel $role = null, string $email = null, array $metaData = []): Invite
    {
        return m::spy(Invite::class)
            ->shouldReceive('getRole')
            ->andReturn($role ?: $this->createRoleModel())
            ->getMock()
            ->shouldReceive('getEmail')
            ->andReturn($email ?: $this->getFaker()->safeEmail)
            ->getMock()
            ->shouldReceive('getMetaData')
            ->andReturn($metaData)
            ->getMock();
    }

    /**
     * @return AcceptInvitation|MockInterface
     */
    private function createAcceptInvitationRequest(array $metaData = []): AcceptInvitation
    {
        return m::spy(AcceptInvitation::class)
            ->shouldReceive('getMetaData')
            ->andReturn($metaData)
            ->getMock();
    }

    private function setUpInviteTest(bool $matchingProjects = true): array
    {
        $project = $this->createProjectModel();
        $this->mockModelGetId($project, $this->getFaker()->numberBetween(1));
        $otherProject = $this->createProjectModel();
        $this->mockModelGetId($otherProject, $project->getId() + 1);
        $email = $this->getFaker()->safeEmail;
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $role = $this->createRoleModel();
        $this->mockRoleModelGetProject($role, $matchingProjects ? $project : $otherProject);
        $request = $this->createInviteRequest($role, $email, $metaData);
        $invitationData = [$this->getFaker()->word => $this->getFaker()->word];
        $invitation = $this->createInvitationModel();
        $this->mockInvitationModelToArray($invitation, $invitationData);
        $invitationManager = $this->createInvitationManager();
        $this->mockinvitationManagerInviteMember($invitationManager, $invitation, $role, $email, $metaData);
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $response, ['invitation' => $invitationData], 201);
        $invitationsController = $this->getInvitationsController($invitationManager, $responseFactory);

        return [$invitationsController, $project, $request, $response];
    }

    public function testInvite(): void
    {
        [$projectsController, $project, $request, $response] = $this->setUpInviteTest();

        $this->assertEquals($response, $projectsController->invite($project, $request));
    }

    public function testInviteWithoutMatchingProjects(): void
    {
        [$projectsController, $project, $request] = $this->setUpInviteTest(matchingProjects: false);

        $this->expectException(RoleProjectNotAllowedException::class);

        $projectsController->invite($project, $request);
    }

    public function testAcceptInvitation(): void
    {
        $invitation = $this->createInvitationModel();
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $request = $this->createAcceptInvitationRequest($metaData);
        $user = $this->createUserModel();
        $authManager = $this->createAuthManager();
        $this->mockAuthManagerAuthenticatedUser($authManager, $user);
        $invitationManager = $this->createInvitationManager();
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $response, [], 201);

        $this->assertEquals(
            $response,
            $this->getInvitationsController($invitationManager, $responseFactory)->acceptInvitation(
                $invitation,
                $request,
                $authManager
            )
        );
        $this->assertInvitationManagerAcceptInvitation($invitationManager, $invitation, $user, $metaData);
    }

    public function testDeclineInvitation(): void
    {
        $invitation = $this->createInvitationModel();
        $invitationManager = $this->createInvitationManager();
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $response, [], 204);

        $this->assertEquals(
            $response,
            $this->getInvitationsController($invitationManager, $responseFactory)->declineInvitation($invitation)
        );
        $this->assertInvitationManagerDeclineInvitation($invitationManager, $invitation);
    }
}
