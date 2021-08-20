<?php

namespace Tests\Unit\Projects\Invitations;

use App\Models\DatabaseHandler;
use App\Projects\Invites\InvitationDoctrineRepository;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

final class InvitationDoctrineRepositoryTest extends TestCase
{
    use ModelHelper;
    use ProjectHelper;

    public function testFindOneByUuid(): void
    {
        $uuid = $this->getFaker()->uuid;
        $invitation = $this->createInvitationModel();
        $databaseHandler = $this->createDatabaseHandler();
        $this->mockDatabaseHandlerLoad($databaseHandler, $invitation, ['uuid' => $uuid]);

        $this->assertEquals($invitation, $this->getInvitationRepository($databaseHandler)->findOneByUuid($uuid));
    }

    public function testFindOneByUuidWithoutInvitation(): void
    {
        $uuid = $this->getFaker()->uuid;
        $databaseHandler = $this->createDatabaseHandler();
        $this->mockDatabaseHandlerLoad($databaseHandler, null, ['uuid' => $uuid]);

        $this->assertNull($this->getInvitationRepository($databaseHandler)->findOneByUuid($uuid));
    }

    private function getInvitationRepository(DatabaseHandler $databaseHandler = null): InvitationDoctrineRepository
    {
        return new InvitationDoctrineRepository($databaseHandler ?: $this->createDatabaseHandler());
    }
}
