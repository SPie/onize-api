<?php

namespace Tests\Unit\Projects\Invitations;

use App\Projects\Invites\InvitationDoctrineModel;
use App\Projects\RoleModel;
use Carbon\CarbonImmutable;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class InvitationDoctrineModelTest
 *
 * @package Tests\Unit\Projects\Invitations
 */
final class InvitationDoctrineModelTest extends TestCase
{
    use ProjectHelper;

    //region Tests

    public function testToArray(): void
    {
        $uuid = $this->getFaker()->uuid;
        $email = $this->getFaker()->safeEmail;
        $roleData = [$this->getFaker()->word => $this->getFaker()->word];
        $role = $this->createRoleModel();
        $this->mockRoleModelToArray($role, $roleData, true);
        $validUntil = $this->getCarbonImmutable();
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $acceptedAt = $this->getCarbonImmutable();
        $declinedAt = $this->getCarbonImmutable();

        $this->assertEquals(
            [
                'uuid'       => $uuid,
                'email'      => $email,
                'role'       => $roleData,
                'validUntil' => $validUntil->format('Y-m-d H:i:s'),
                'metaData'   => $metaData,
                'acceptedAt' => $acceptedAt->format('Y-m-d H:i:s'),
                'declinedAt' => $declinedAt->format('Y-m-d H:i:s'),
            ],
            $this->getInvitationDoctrineModel($role, $email, $validUntil, $metaData)
                ->setUuid($uuid)
                ->setAcceptedAt($acceptedAt)
                ->setDeclinedAt($declinedAt)
                ->toArray()
        );
    }

    public function testIsExpired(): void
    {
        $now = new CarbonImmutable();
        $this->setCarbonMock($now);
        $validUntil = $now->addDay();

        $this->assertFalse($this->getInvitationDoctrineModel(null, null, $validUntil)->isExpired());
    }

    public function testIsExpiredWithExpiredInvitation(): void
    {
        $now = new CarbonImmutable();
        $this->setCarbonMock($now);
        $validUntil = $now->subDay();

        $this->assertTrue($this->getInvitationDoctrineModel(null, null, $validUntil)->isExpired());
    }

    //endregion

    private function getInvitationDoctrineModel(
        RoleModel $roleModel = null,
        string $email = null,
        \DateTimeImmutable $validUntil = null,
        array $metaData = []
    ): InvitationDoctrineModel {
        return new InvitationDoctrineModel(
            $roleModel ?: $this->createRoleModel(),
            $email ?: $this->getFaker()->safeEmail,
            $validUntil ?: new CarbonImmutable($this->getFaker()->dateTime),
            $metaData
        );
    }
}
