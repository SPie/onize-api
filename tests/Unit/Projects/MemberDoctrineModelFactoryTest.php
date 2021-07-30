<?php

namespace Tests\Unit\Projects;

use App\Projects\MemberDoctrineModel;
use App\Projects\MemberDoctrineModelFactory;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class MemberDoctrineModelFactoryTest
 *
 * @package Tests\Unit\Projects
 */
final class MemberDoctrineModelFactoryTest extends TestCase
{
    use ProjectHelper;
    use UsersHelper;

    //region Tests

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $user = $this->createUserModel();
        $role = $this->createRoleModel();
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];

        $this->assertEquals(
            new MemberDoctrineModel($user, $role, $metaData),
            $this->getMemberDoctrineModelFactory()->create($user, $role, $metaData)
        );
    }

    //endregion

    /**
     * @return MemberDoctrineModelFactory
     */
    private function getMemberDoctrineModelFactory(): MemberDoctrineModelFactory
    {
        return new MemberDoctrineModelFactory();
    }
}
