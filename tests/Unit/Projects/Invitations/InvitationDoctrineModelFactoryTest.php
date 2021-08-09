<?php

namespace Tests\Unit\Projects\Invitations;

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

    public function testCreate(): void
    {
        $role = $this->createRoleModel();
        $email = $this->getFaker()->safeEmail;
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $now = new CarbonImmutable();
        $this->setCarbonMock($now);
        $validUntilMinutes = $this->getFaker()->numberBetween(1, 44640);

        $this->assertEquals(
            new InvitationDoctrineModel($role, $email, $now->addMinutes($validUntilMinutes), $metaData),
            $this->getInvitationDoctrineModelFactory($validUntilMinutes)->create($role, $email, $metaData)
        );
    }

    //endregion

    private function getInvitationDoctrineModelFactory(int $validUntilMinutes = null): InvitationDoctrineModelFactory
    {
        return new InvitationDoctrineModelFactory(
            $validUntilMinutes ?: $this->getFaker()->numberBetween(1, 44640)
        );
    }
}
