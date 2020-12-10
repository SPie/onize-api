<?php

namespace App\Http\Controllers;

use App\Auth\AuthManager;
use App\Http\Requests\Projects\Create;
use App\Projects\ProjectManager;
use App\Projects\RoleManager;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

/**
 * Class ProjectsController
 *
 * @package App\Http\Controllers
 */
final class ProjectsController extends Controller
{
    const ROUTE_NAME_CREATE = 'projects.create';

    const RESPONSE_PARAMETER_PROJECT = 'project';

    /**
     * @var ProjectManager
     */
    private ProjectManager $projectManager;

    /**
     * ProjectsController constructor.
     *
     * @param ProjectManager  $projectManager
     * @param ResponseFactory $responseFactory
     */
    public function __construct(ProjectManager $projectManager, ResponseFactory $responseFactory)
    {
        parent::__construct($responseFactory);

        $this->projectManager = $projectManager;
    }

    /**
     * @return ProjectManager
     */
    private function getProjectManager(): ProjectManager
    {
        return $this->projectManager;
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
        $project = $this->getProjectManager()->createProject(
            $request->getLabel(),
            $request->getDescription(),
            $request->getMetaDataElements()
        );

        $roleManager->createOwnerRole($project, $authManager->authenticatedUser(), $request->getMetaData());

        return $this->getResponseFactory()->json(
            [self::RESPONSE_PARAMETER_PROJECT => $project->toArray()],
            JsonResponse::HTTP_CREATED
        );
    }

    //endregion
}
