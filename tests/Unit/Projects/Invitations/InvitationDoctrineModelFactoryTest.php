<?php

namespace Tests\Unit\Projects\Invitations;

use App\Projects\Invites\InvitationDoctrineModel;
use App\Projects\Invites\InvitationDoctrineModelFactory;
use Carbon\CarbonImmutable;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class InvitationDoctrineModelFactoryTest
 *
 * @package Tests\Unit\Projects\Invitations
 */
final class InvitationDoctrineModelFactoryTest extends TestCase
{
    use ProjectHelper;

    //region Tests

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $role = $this->createRoleModel();
        $email = $this->getFaker()->safeEmail;
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $validUntil = new CarbonImmutable();
        $this->setCarbonMock($validUntil);

        $this->assertEquals(
            new InvitationDoctrineModel($role, $email, $validUntil, $metaData),
            $this->getInvitationDoctrineModelFactory()->create($role, $email, $metaData)
        );
    }

    //endregion

    /**
     * @return InvitationDoctrineModelFactory
     */
    private function getInvitationDoctrineModelFactory(): InvitationDoctrineModelFactory
    {
        return new InvitationDoctrineModelFactory();
    }
}
