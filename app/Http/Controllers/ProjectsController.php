<?php

namespace App\Http\Controllers;

use App\Auth\AuthManager;
use App\Http\Requests\Projects\Create;
use App\Http\Requests\Projects\Invite;
use App\Projects\Invites\InvitationManager;
use App\Projects\MemberModel;
use App\Projects\ProjectManager;
use App\Projects\ProjectModel;
use App\Projects\RoleManager;
use App\Projects\RoleModel;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

/**
 * Class ProjectsController
 *
 * @package App\Http\Controllers
 */
final class ProjectsController extends Controller
{
    public const ROUTE_NAME_CREATE         = 'projects.create';
    public const ROUTE_NAME_USERS_PROJECTS = 'projects.usersProjects';
    public const ROUTE_NAME_SHOW           = 'projects.show';
    public const ROUTE_NAME_MEMBERS        = 'projects.members';
    public const ROUTE_NAME_INVITE         = 'projects.invitations.invite';

    private const RESPONSE_PARAMETER_PROJECT    = 'project';
    private const RESPONSE_PARAMETER_PROJECTS   = 'projects';
    private const RESPONSE_PARAMETER_MEMBERS    = 'members';
    private const RESPONSE_PARAMETER_INVITATION = 'invitation';

    /**
     * ProjectsController constructor.
     *
     * @param ProjectManager  $projectManager
     * @param ResponseFactory $responseFactory
     */
    public function __construct(private ProjectManager $projectManager, ResponseFactory $responseFactory)
    {
        parent::__construct($responseFactory);
    }

    //region Controller actions

    /**
     * @param Create      $request
     * @param AuthManager $authManager
     * @param RoleManager $roleManager
     *
     * @return JsonResponse
     */
    public function create(Create $request, AuthManager $authManager, RoleManager $roleManager): JsonResponse
    {
        $project = $this->projectManager->createProject(
            $request->getLabel(),
            $request->getDescription(),
            $request->getMetaDataElements()
        );

        $project->addRole(
            $roleManager->createOwnerRole($project, $authManager->authenticatedUser(), $request->getMetaData())
        );

        return $this->getResponseFactory()->json(
            [self::RESPONSE_PARAMETER_PROJECT => $project->toArray()],
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * @param AuthManager $authManager
     *
     * @return JsonResponse
     */
    public function usersProjects(AuthManager $authManager): JsonResponse
    {
        return $this->getResponseFactory()->json([
            self::RESPONSE_PARAMETER_PROJECTS => $authManager->authenticatedUser()->getMembers()
                ->map(fn (MemberModel $member) => $member->getRole()->toArray(true))
                ->toArray()
        ]);
    }

    /**
     * @param ProjectModel $project
     *
     * @return JsonResponse
     */
    public function show(ProjectModel $project): JsonResponse
    {
        return $this->getResponseFactory()->json([self::RESPONSE_PARAMETER_PROJECT => $project->toArray()]);
    }

    /**
     * @param ProjectModel $project
     *
     * @return JsonResponse
     */
    public function members(ProjectModel $project): JsonResponse
    {
        return $this->getResponseFactory()->json([
            self::RESPONSE_PARAMETER_MEMBERS => $project->getMembers()
                ->map(
                    fn (MemberModel $member) => \array_merge(
                        $member->getUser()->toArray(),
                        [MemberModel::PROPERTY_META_DATA => $member->getMetaData()]
                    )
                )
                ->getValues()
        ]);
    }

    /**
     * @param RoleModel         $role
     * @param Invite            $invite
     * @param InvitationManager $invitationManager
     *
     * @return JsonResponse
     */
    public function invite(RoleModel $role, Invite $invite, InvitationManager $invitationManager): JsonResponse
    {
        return $this->getResponseFactory()->json(
            [
                self::RESPONSE_PARAMETER_INVITATION => $invitationManager->inviteMember(
                    $role,
                    $invite->getEmail(),
                    $invite->getMetaData()
                )->toArray()
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    //endregion
}
