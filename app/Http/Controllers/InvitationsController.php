<?php

namespace App\Http\Controllers;

use App\Auth\AuthManager;
use App\Http\Requests\Projects\AcceptInvitation;
use App\Http\Requests\Projects\Invite;
use App\Projects\Invites\Exceptions\RoleProjectNotAllowedException;
use App\Projects\Invites\InvitationManager;
use App\Projects\Invites\InvitationModel;
use App\Projects\ProjectModel;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

final class InvitationsController extends Controller
{
    public const ROUTE_NAME_INVITE             = 'projects.invitations.invite';
    public const ROUTE_NAME_ACCEPT_INVITATION  = 'projects.invitations.accept';
    public const ROUTE_NAME_DECLINE_INVITATION = 'projects.invitations.decline';

    private const RESPONSE_PARAMETER_INVITATION = 'invitation';

    public function __construct(private InvitationManager $invitationManager, ResponseFactory $responseFactory)
    {
        parent::__construct($responseFactory);
    }

    public function invite(ProjectModel $project, Invite $invite): JsonResponse
    {
        if ($project->getId() !== $invite->getRole()->getProject()->getId()) {
            throw new RoleProjectNotAllowedException('Invite for the roles project not allowed.');
        }

        return $this->getResponseFactory()->json(
            [
                self::RESPONSE_PARAMETER_INVITATION => $this->invitationManager->inviteMember(
                    $invite->getRole(),
                    $invite->getEmail(),
                    $invite->getMetaData()
                )->toArray()
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    public function acceptInvitation(InvitationModel $invitation, AcceptInvitation $request, AuthManager $authManager): JsonResponse
    {
        $this->invitationManager->acceptInvitation($invitation, $authManager->authenticatedUser(), $request->getMetaData());

        return $this->getResponseFactory()->json([], JsonResponse::HTTP_CREATED);
    }

    public function declineInvitation(InvitationModel $invitation): JsonResponse
    {
        $this->invitationManager->declineInvitation($invitation);

        return $this->getResponseFactory()->json([], JsonResponse::HTTP_NO_CONTENT);
    }
}
