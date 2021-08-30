<?php

namespace App\Projects\Invites;

use App\Emails\EmailService;
use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\Invites\Exceptions\AlreadyAcceptedException;
use App\Projects\Invites\Exceptions\AlreadyMemberException;
use App\Projects\Invites\Exceptions\InvitationDeclinedException;
use App\Projects\Invites\Exceptions\InvitationExpiredException;
use App\Projects\MemberModel;
use App\Projects\MemberModelFactory;
use App\Projects\MemberRepository;
use App\Projects\RoleModel;
use App\Users\UserModel;
use Carbon\CarbonImmutable;

class InvitationManager
{
    public function __construct(
        private InvitationRepository $invitationRepository,
        private InvitationModelFactory $invitationModelFactory,
        private EmailService $emailService,
        private MemberRepository $memberRepository,
        private MemberModelFactory $memberModelFactory
    ) {
    }

    public function getInvitation(string $uuid): InvitationModel
    {
        $invitation = $this->invitationRepository->findOneByUuid($uuid);
        if (!$invitation) {
            throw new ModelNotFoundException(\sprintf('Invitation with uuid %s not found.', $uuid));
        }

        return $invitation;
    }

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

    public function acceptInvitation(InvitationModel $invitation, UserModel $user, array $metaData): MemberModel
    {
        $this->validateInvitation($invitation);

        $invitation->setAcceptedAt(new CarbonImmutable());

        return $this->memberRepository->save($this->memberModelFactory->create($user, $invitation->getRole(), $metaData));
    }

    public function declineInvitation(InvitationModel $invitation): InvitationModel
    {
        $this->validateInvitation($invitation);

        return $this->invitationRepository->save($invitation->setDeclinedAt(new CarbonImmutable()));
    }

    private function validateInvitation(InvitationModel $invitation): self
    {
        if ($invitation->isExpired()) {
            throw new InvitationExpiredException('Invitation is expired.');
        }
        if ($invitation->getAcceptedAt()) {
            throw new AlreadyAcceptedException('Invitation was already accepted.');
        }
        if ($invitation->getDeclinedAt()) {
            throw new InvitationDeclinedException('Invitation was declined.');
        }

        return $this;
    }
}
