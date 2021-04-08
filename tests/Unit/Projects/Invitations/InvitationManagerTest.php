<?php

namespace Tests\Unit\Projects\Invitations;

use App\Emails\EmailService;
use App\Projects\Invites\Exceptions\AlreadyMemberException;
use App\Projects\Invites\InvitationManager;
use App\Projects\Invites\InvitationModelFactory;
use App\Projects\Invites\InvitationRepository;
use Tests\Helper\EmailsHelper;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class InvitationManagerTest
 *
 * @package Tests\Unit\Projects\Invitations
 */
final class InvitationManagerTest extends TestCase
{
    use EmailsHelper;
    use ModelHelper;
    use ProjectHelper;

    //region Tests

    /**
     * @param bool $alreadyMember
     * @param bool $withExceptionOnEmail
     *
     * @return array
     */
    private function setUpInviteMemberTest(bool $alreadyMember = false, bool $withExceptionOnEmail = false): array
    {
        $email = $this->getFaker()->safeEmail;
        $project = $this->createProjectModel();
        $this->mockProjectModelHasMemberWithEmail($project, $alreadyMember, $email);
        $role = $this->createRoleModel();
        $this->mockRoleModelGetProject($role, $project);
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $invitation = $this->createInvitationModel();
        $this->mockUuidModelGetUuid($invitation, $this->getFaker()->uuid);
        $invitationModelFactory = $this->createInvitationModelFactory();
        $this->mockInvitationModelFactoryCreate($invitationModelFactory, $invitation, $role, $email, $metaData);
        $invitationRepository = $this->createInvitationRepository();
        $emailService = $this->createEmailService();
        if (!$alreadyMember) {
            $this->mockRepositorySave($invitationRepository, $invitation);
            $this->mockEmailServiceSendInvitationEmail(
                $emailService,
                $email,
                $invitation->getUuid(),
                $role,
                $withExceptionOnEmail ? new \Exception() : null
            );
        }
        $invitationManager = $this->getInvitationManager(
            $invitationRepository,
            $invitationModelFactory,
            $emailService
        );

        return [$invitationManager, $role, $email, $metaData, $invitation];
    }

    /**
     * @return void
     */
    public function testInviteMember(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $role, $email, $metaData, $invitation] = $this->setUpInviteMemberTest();

        $this->assertEquals($invitation, $invitationManager->inviteMember($role, $email, $metaData));
    }

    /**
     * @return void
     */
    public function testInviteMemberWithEmailAlreadyMember(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $role, $email, $metaData] = $this->setUpInviteMemberTest(true);

        $this->expectException(AlreadyMemberException::class);

        $invitationManager->inviteMember($role, $email, $metaData);
    }

    /**
     * @return void
     */
    public function testInviteMemberWithExceptionOnSendingEmail(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $role, $email, $metaData, $invitation] = $this->setUpInviteMemberTest(false, true);

        $this->assertEquals($invitation, $invitationManager->inviteMember($role, $email, $metaData));
    }

    //endregion

    /**
     * @param InvitationRepository|null   $invitationRepository
     * @param InvitationModelFactory|null $invitationModelFactory
     * @param EmailService|null           $emailService
     *
     * @return InvitationManager
     */
    private function getInvitationManager(
        InvitationRepository $invitationRepository = null,
        InvitationModelFactory $invitationModelFactory = null,
        EmailService $emailService = null
    ): InvitationManager {
        return new InvitationManager(
            $invitationRepository ?: $this->createInvitationRepository(),
            $invitationModelFactory ?: $this->createInvitationModelFactory(),
            $emailService ?: $this->createEmailService()
        );
    }
}
