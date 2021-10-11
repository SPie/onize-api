<?php

namespace App\Http\Controllers;

use App\Auth\AuthManager;
use App\Http\Requests\Projects\ChangeRole;
use App\Http\Requests\Projects\Create;
use App\Http\Requests\Projects\CreateRole;
use App\Projects\MemberModel;
use App\Projects\ProjectManager;
use App\Projects\ProjectModel;
use App\Projects\RoleManager;
use App\Users\UserModel;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

final class ProjectsController extends Controller
{
    public const ROUTE_NAME_CREATE             = 'projects.create';
    public const ROUTE_NAME_USERS_PROJECTS     = 'projects.usersProjects';
    public const ROUTE_NAME_SHOW               = 'projects.show';
    public const ROUTE_NAME_MEMBERS            = 'projects.members';
    public const ROUTE_NAME_REMOVE_MEMBER      = 'projects.members.remove';
    public const ROUTE_NAME_CREATE_ROLE        = 'projects.roles.create';
    public const ROUTE_NAME_CHANGE_ROLE        = 'projects.roles.change';

    private const RESPONSE_PARAMETER_PROJECT    = 'project';
    private const RESPONSE_PARAMETER_PROJECTS   = 'projects';
    private const RESPONSE_PARAMETER_MEMBERS    = 'members';
    private const RESPONSE_PARAMETER_ROLE       = 'role';

    public function __construct(private ProjectManager $projectManager, ResponseFactory $responseFactory)
    {
        parent::__construct($responseFactory);
    }

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

    public function removeMember(ProjectModel $project, UserModel $user): JsonResponse
    {
        $this->projectManager->removeMember($project, $user);

        return $this->getResponseFactory()->json([], JsonResponse::HTTP_NO_CONTENT);
    }

    public function createRole(ProjectModel $project, CreateRole $request, RoleManager $roleManager): JsonResponse
    {
        $role = $roleManager->createRole(
            $project,
            $request->getLabel(),
            $request->getPermissions()
        );

        return $this->getResponseFactory()->json(
            [self::RESPONSE_PARAMETER_ROLE => $role->toArray()],
            JsonResponse::HTTP_CREATED
        );
    }

    public function changeRole(ChangeRole $request): JsonResponse
    {
        $this->projectManager->changeRole($request->getUser(), $request->getRole());

        return $this->getResponseFactory()->json([], JsonResponse::HTTP_NO_CONTENT);
    }
}
