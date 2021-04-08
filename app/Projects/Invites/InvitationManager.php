<?php

namespace App\Projects\Invites;

use App\Emails\EmailService;
use App\Projects\Invites\Exceptions\AlreadyMemberException;
use App\Projects\RoleModel;

/**
 * Class InvitationManager
 *
 * @package App\Projects\Invites
 */
class InvitationManager
{
    /**
     * @var InvitationRepository
     */
    private InvitationRepository $invitationRepository;

    /**
     * @var InvitationModelFactory
     */
    private InvitationModelFactory $invitationModelFactory;

    /**
     * @var EmailService
     */
    private EmailService $emailService;

    /**
     * InvitationManager constructor.
     *
     * @param InvitationRepository   $invitationRepository
     * @param InvitationModelFactory $invitationModelFactory
     * @param EmailService           $emailService
     */
    public function __construct(
        InvitationRepository $invitationRepository,
        InvitationModelFactory $invitationModelFactory,
        EmailService $emailService
    ) {
        $this->invitationRepository = $invitationRepository;
        $this->invitationModelFactory = $invitationModelFactory;
        $this->emailService = $emailService;
    }

    /**
     * @param RoleModel $role
     * @param string    $email
     * @param array     $metaData
     *
     * @return InvitationModel
     */
    public function inviteMember(RoleModel $role, string $email, array $metaData = []): InvitationModel
    {
        if ($role->getProject()->hasMemberWithEmail($email)) {
            throw new AlreadyMemberException(\sprintf('%s is already member of project %s', $email, $role->getUuid()));
        }

        $invitation = $this->invitationRepository->save($this->invitationModelFactory->create($role, $email, $metaData));

        try {
            $this->emailService->sendInvitationEmail($email, $invitation->getUuid(), $role);
        } catch (\Exception $e) {
        }

        return $invitation;
    }
}
