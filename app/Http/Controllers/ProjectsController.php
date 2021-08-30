<?php

namespace App\Http\Controllers;

use App\Auth\AuthManager;
use App\Http\Requests\Projects\Create;
use App\Projects\MemberModel;
use App\Projects\ProjectManager;
use App\Projects\ProjectModel;
use App\Projects\RoleManager;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

final class ProjectsController extends Controller
{
    public const ROUTE_NAME_CREATE             = 'projects.create';
    public const ROUTE_NAME_USERS_PROJECTS     = 'projects.usersProjects';
    public const ROUTE_NAME_SHOW               = 'projects.show';
    public const ROUTE_NAME_MEMBERS            = 'projects.members';
    public const ROUTE_NAME_INVITE             = 'projects.invitations.invite';
    public const ROUTE_NAME_ACCEPT_INVITATION  = 'projects.invitations.accept';
    public const ROUTE_NAME_DECLINE_INVITATION = 'projects.invitations.decline';

    private const RESPONSE_PARAMETER_PROJECT    = 'project';
    private const RESPONSE_PARAMETER_PROJECTS   = 'projects';
    private const RESPONSE_PARAMETER_MEMBERS    = 'members';

    public function __construct(private ProjectManager $projectManager, ResponseFactory $responseFactory)
    {
        parent::__construct($responseFactory);
    }

    //region Controller actions

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

    public function usersProjects(AuthManager $authManager): JsonResponse
    {
        return $this->getResponseFactory()->json([
            self::RESPONSE_PARAMETER_PROJECTS => $authManager->authenticatedUser()->getMembers()
                ->map(fn (MemberModel $member) => $member->getRole()->toArray(true))
                ->toArray()
        ]);
    }

    public function show(ProjectModel $project): JsonResponse
    {
        return $this->getResponseFactory()->json([self::RESPONSE_PARAMETER_PROJECT => $project->toArray()]);
    }

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

    //endregion
}
