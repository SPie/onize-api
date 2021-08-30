<?php

namespace App\Http\Controllers;

use App\Auth\AuthManager;
use App\Http\Requests\Projects\AcceptInvitation;
use App\Http\Requests\Projects\Invite;
use App\Projects\Invites\InvitationManager;
use App\Projects\Invites\InvitationModel;
use App\Projects\RoleModel;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

final class InvitationsController extends Controller
{
    private const RESPONSE_PARAMETER_INVITATION = 'invitation';

    public function __construct(private InvitationManager $invitationManager, ResponseFactory $responseFactory)
    {
        parent::__construct($responseFactory);
    }

    public function invite(RoleModel $role, Invite $invite): JsonResponse
    {
        return $this->getResponseFactory()->json(
            [
                self::RESPONSE_PARAMETER_INVITATION => $this->invitationManager->inviteMember(
                    $role,
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
