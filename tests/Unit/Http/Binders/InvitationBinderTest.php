<?php

namespace Tests\Unit\Http\Binders;

use App\Http\Binders\InvitationBinder;
use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\Invites\InvitationManager;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

final class InvitationBinderTest extends TestCase
{
    use ProjectHelper;

    private function setUpBindTest(bool $withInvitation = true): array
    {
        $uuid = $this->getFaker()->uuid;
        $invitation = $this->createInvitationModel();
        $invitationManager = $this->createInvitationManager();
        $this->mockInvitationManagerGetInvitation($invitationManager, $withInvitation ? $invitation : new ModelNotFoundException(), $uuid);
        $binder = $this->getInvitationBinder($invitationManager);

        return [$binder, $uuid, $invitation];
    }

    public function testBindWithInvitation(): void
    {
        /** @var InvitationBinder $binder */
        [$binder, $uuid, $invitation] = $this->setUpBindTest();

        $this->assertEquals($invitation, $binder->bind($uuid));
    }

    public function testBindWithoutInvitation(): void
    {
        /** @var InvitationBinder $binder */
        [$binder, $uuid] = $this->setUpBindTest(false);

        $this->expectException(ModelNotFoundException::class);

        $binder->bind($uuid);
    }

    private function getInvitationBinder(InvitationManager $invitationManager = null): InvitationBinder
    {
        return new InvitationBinder($invitationManager ?: $this->createInvitationManager());
    }
}
