<?php

namespace Tests\Unit\Projects\Invitations;

use App\Emails\EmailService;
use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\Invites\Exceptions\AlreadyAcceptedException;
use App\Projects\Invites\Exceptions\AlreadyMemberException;
use App\Projects\Invites\Exceptions\InvitationDeclinedException;
use App\Projects\Invites\Exceptions\InvitationExpiredException;
use App\Projects\Invites\InvitationManager;
use App\Projects\Invites\InvitationModelFactory;
use App\Projects\Invites\InvitationRepository;
use App\Projects\MemberModelFactory;
use App\Projects\MemberRepository;
use Carbon\CarbonImmutable;
use Tests\Helper\EmailsHelper;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class InvitationManagerTest extends TestCase
{
    use EmailsHelper;
    use ModelHelper;
    use ProjectHelper;
    use UsersHelper;

    //region Tests

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

    public function testInviteMember(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $role, $email, $metaData, $invitation] = $this->setUpInviteMemberTest();

        $this->assertEquals($invitation, $invitationManager->inviteMember($role, $email, $metaData));
    }

    public function testInviteMemberWithEmailAlreadyMember(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $role, $email, $metaData] = $this->setUpInviteMemberTest(true);

        $this->expectException(AlreadyMemberException::class);

        $invitationManager->inviteMember($role, $email, $metaData);
    }

    public function testInviteMemberWithExceptionOnSendingEmail(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $role, $email, $metaData, $invitation] = $this->setUpInviteMemberTest(false, true);

        $this->assertEquals($invitation, $invitationManager->inviteMember($role, $email, $metaData));
    }

    private function setUpAcceptInvitationTest(
        bool $invitationExpired = false,
        bool $invitationAccepted = false,
        bool $invitationDeclined = false
    ): array {
        $now = new CarbonImmutable();
        $this->setCarbonMock($now);
        $role = $this->createRoleModel();
        $invitation = $this->createInvitationModel();
        $this
            ->mockInvitationModelGetRole($invitation, $role)
            ->mockInvitationModelIsExpired($invitation, $invitationExpired)
            ->mockInvitationModelGetAcceptedAt($invitation, $invitationAccepted ? new \DateTimeImmutable() : null)
            ->mockInvitationModelGetDeclinedAt($invitation, $invitationDeclined ? new \DateTimeImmutable() : null);
        $user = $this->createUserModel();
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $member = $this->createMemberModel();
        $memberModelFactory = $this->createMemberModelFactory();
        $this->mockMemberModelFactoryCreate($memberModelFactory, $member, $user, $role, $metaData);
        $memberRepository = $this->createMemberRepository();
        if (!$invitationExpired && !$invitationAccepted && !$invitationDeclined) {
            $this->mockRepositorySave($memberRepository, $member);
        }
        $invitationManager = $this->getInvitationManager(null, null, null, $memberRepository, $memberModelFactory);

        return [$invitationManager, $invitation, $user, $metaData, $member, $now];
    }

    public function testAcceptInvitation(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $invitation, $user, $metaData, $member, $now] = $this->setUpAcceptInvitationTest();

        $this->assertEquals($member, $invitationManager->acceptInvitation($invitation, $user, $metaData));
        $this->assertInvitationModelSetAcceptedAt($invitation, $now);
    }

    public function testAcceptInvitationInvalidInvitation(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $invitation, $user, $metaData] = $this->setUpAcceptInvitationTest(true);

        $this->expectException(InvitationExpiredException::class);

        $invitationManager->acceptInvitation($invitation, $user, $metaData);
    }

    public function testAcceptInvitationWithAlreadyAcceptedInvitation(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $invitation, $user, $metaData] = $this->setUpAcceptInvitationTest(false, true);

        $this->expectException(AlreadyAcceptedException::class);

        $invitationManager->acceptInvitation($invitation, $user, $metaData);
    }

    public function testAcceptInvitationWithDeclinedInvitation(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $invitation, $user, $metaData] = $this->setUpAcceptInvitationTest(false, false, true);

        $this->expectException(InvitationDeclinedException::class);

        $invitationManager->acceptInvitation($invitation, $user, $metaData);
    }

    private function setUpGetInvitationTest(bool $withInvitation = true): array
    {
        $uuid = $this->getFaker()->uuid;
        $invitation = $this->createInvitationModel();
        $invitationRepository = $this->createInvitationRepository();
        $this->mockInvitationRepositoryFindOneByUuid($invitationRepository, $withInvitation ? $invitation : null, $uuid);
        $invitationManager = $this->getInvitationManager($invitationRepository);

        return [$invitationManager, $uuid, $invitation];
    }

    public function testGetInvitation(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $uuid, $invitation] = $this->setUpGetInvitationTest();

        $this->assertEquals($invitation, $invitationManager->getInvitation($uuid));
    }

    public function testGetInvitationWithoutInvitation(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $uuid] = $this->setUpGetInvitationTest(false);

        $this->expectException(ModelNotFoundException::class);

        $invitationManager->getInvitation($uuid);
    }

    private function setUpDeclineInvitationTest(
        bool $alreadyAccepted = false,
        bool $validInvitation = true,
        bool $alreadyDeclined = false
    ): array {
        $now = new CarbonImmutable();
        $this->setCarbonMock($now);
        $invitation = $this->createInvitationModel();
        $this
            ->mockInvitationModelSetDeclinedAt($invitation, $now)
            ->mockInvitationModelIsExpired($invitation, !$validInvitation)
            ->mockInvitationModelGetDeclinedAt($invitation, $alreadyDeclined ? new CarbonImmutable() : null)
            ->mockInvitationModelGetAcceptedAt($invitation, $alreadyAccepted ? new CarbonImmutable() : null);
        $invitationRepository = $this->createInvitationRepository();
        if (!$alreadyAccepted && $validInvitation && !$alreadyDeclined) {
            $this->mockRepositorySave($invitationRepository, $invitation);
        }
        $invitationManager = $this->getInvitationManager($invitationRepository);

        return [$invitationManager, $invitation, $now];
    }

    public function testDeclineInvitation(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $invitation, $now] = $this->setUpDeclineInvitationTest();

        $this->assertEquals($invitation, $invitationManager->declineInvitation($invitation));
        $this->assertInvitationModelSetDeclinedAt($invitation, $now);
    }

    public function testDeclineInvitationForAlreadyAcceptedInvitation(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $invitation] = $this->setUpDeclineInvitationTest(true);

        $this->expectException(AlreadyAcceptedException::class);

        $invitationManager->declineInvitation($invitation);
    }

    public function testDeclineInvitationForAlreadyExpiredInvitation(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $invitation] = $this->setUpDeclineInvitationTest(false, false);

        $this->expectException(InvitationExpiredException::class);

        $invitationManager->declineInvitation($invitation);
    }

    public function testDeclineInvitationForAlreadyDeclinedInvitation(): void
    {
        /** @var InvitationManager $invitationManager */
        [$invitationManager, $invitation] = $this->setUpDeclineInvitationTest(false, true, true);

        $this->expectException(InvitationDeclinedException::class);

        $invitationManager->declineInvitation($invitation);
    }

    //endregion

    private function getInvitationManager(
        InvitationRepository $invitationRepository = null,
        InvitationModelFactory $invitationModelFactory = null,
        EmailService $emailService = null,
        MemberRepository $memberRepository = null,
        MemberModelFactory $memberModelFactory = null
    ): InvitationManager {
        return new InvitationManager(
            $invitationRepository ?: $this->createInvitationRepository(),
            $invitationModelFactory ?: $this->createInvitationModelFactory(),
            $emailService ?: $this->createEmailService(),
            $memberRepository ?: $this->createMemberRepository(),
            $memberModelFactory ?: $this->createMemberModelFactory()
        );
    }
}
