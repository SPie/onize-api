<?php

namespace Tests\Unit\Projects\Invitations;

use App\Models\UuidGenerator;
use App\Projects\Invites\InvitationDoctrineModel;
use App\Projects\Invites\InvitationDoctrineModelFactory;
use Carbon\CarbonImmutable;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class InvitationDoctrineModelFactoryTest
 *
 * @package Tests\Unit\Projects\Invitations
 */
final class InvitationDoctrineModelFactoryTest extends TestCase
{
    use ModelHelper;
    use ProjectHelper;

    //region Tests

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $uuid = $this->getFaker()->uuid;
        $uuidGenerator = $this->createUuidGenerator($uuid);
        $role = $this->createRoleModel();
        $email = $this->getFaker()->safeEmail;
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $now = new CarbonImmutable();
        $this->setCarbonMock($now);
        $validUntilMinutes = $this->getFaker()->numberBetween(1, 44640);

        $this->assertEquals(
            new InvitationDoctrineModel($uuid, $role, $email, $now->addMinutes($validUntilMinutes), $metaData),
            $this->getInvitationDoctrineModelFactory($uuidGenerator, $validUntilMinutes)->create($role, $email, $metaData)
        );
    }

    //endregion

    /**
     * @return InvitationDoctrineModelFactory
     */
    private function getInvitationDoctrineModelFactory(
        UuidGenerator $uuidGenerator = null,
        int $validUntilMinutes = null
    ): InvitationDoctrineModelFactory {
        return new InvitationDoctrineModelFactory(
            $uuidGenerator ?: $this->createUuidGenerator(),
            $validUntilMinutes ?: $this->getFaker()->numberBetween(1, 44640)
        );
    }
}
