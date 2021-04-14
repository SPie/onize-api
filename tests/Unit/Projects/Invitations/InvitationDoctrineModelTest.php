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

    /**
     * @return void
     */
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
            $this->getInvitationDoctrineModel($uuid, $role, $email, $validUntil, $metaData)
                ->setAcceptedAt($acceptedAt)
                ->setDeclinedAt($declinedAt)
                ->toArray()
        );
    }

    //endregion

    /**
     * @param string|null          $uuid
     * @param RoleModel|null       $roleModel
     * @param string|null          $email
     * @param CarbonImmutable|null $validUntil
     * @param array                $metaData
     *
     * @return InvitationDoctrineModel
     */
    private function getInvitationDoctrineModel(
        string $uuid = null,
        RoleModel $roleModel = null,
        string $email = null,
        CarbonImmutable $validUntil = null,
        array $metaData = []
    ): InvitationDoctrineModel {
        return new InvitationDoctrineModel(
            $uuid ?: $this->getFaker()->uuid,
            $roleModel ?: $this->createRoleModel(),
            $email ?: $this->getFaker()->safeEmail,
            $validUntil ?: new CarbonImmutable($this->getFaker()->dateTime),
            $metaData
        );
    }
}
