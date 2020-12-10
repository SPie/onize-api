<?php

namespace Tests\Unit\Projects;

use App\Projects\MetaDataModelFactory;
use App\Projects\MetaDataRepository;
use App\Projects\RoleManager;
use App\Projects\RoleModelFactory;
use App\Projects\RoleRepository;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class RoleManagerTest
 *
 * @package Tests\Unit\Projects
 */
final class RoleManagerTest extends TestCase
{
    use ModelHelper;
    use ProjectHelper;
    use UsersHelper;

    //region Tests

    /**
     * @return array
     */
    private function setUpCreateOwnerRoleTest(): array
    {
        $project = $this->createProjectModel();
        $user = $this->createUserModel();
        $metaDataName = $this->getFaker()->word;
        $metaDataValue = $this->getFaker()->word;
        $role = $this->createRoleModel();
        $this->mockRoleModelAddUser($role, $user);
        $roleModelFactory = $this->createRoleModelFactory();
        $this->mockRoleModelFactoryCreate($roleModelFactory, $role, $project, 'Owner', true);
        $roleRepository = $this->createRoleRepository();
        $this->mockRepositorySave($roleRepository, $role);
        $metaDataModel = $this->createMetaDataModel();
        $metaDataModelFactory = $this->createMetaDataModelFactory();
        $this->mockMetaDataModelFactoryCreate($metaDataModelFactory, $metaDataModel, $project, $user, $metaDataName, $metaDataValue);
        $metaDataRepository = $this->createMetaDataRepository();
        $this->mockRepositorySave($metaDataRepository, $metaDataModel, false);
        $roleManager = $this->getRoleManager($roleRepository, $roleModelFactory, $metaDataRepository, $metaDataModelFactory);

        return [
            $roleManager,
            $project,
            $user,
            [$metaDataName => $metaDataValue],
            $roleRepository,
            $role,
            $metaDataRepository,
            $metaDataModel,
        ];
    }

    /**
     * @return void
     */
    public function testCreateOwnerRole(): void
    {
        /** @var RoleManager $roleManager */
        [
            $roleManager,
            $project,
            $user,
            $metaData,
            $roleRepository,
            $role,
            $metaDataRepository,
            $metaDataModel,
        ] = $this->setUpCreateOwnerRoleTest();

        $this->assertEquals($role, $roleManager->createOwnerRole($project, $user, $metaData));
        $this
            ->assertRoleModelAddUser($role, $user)
            ->assertRepositorySave($roleRepository, $role)
            ->assertRepositorySave($metaDataRepository, $metaDataModel, false)
            ->assertRepositoryFlush($metaDataRepository);
    }

    //endregion

    /**
     * @param RoleRepository|null       $roleRepository
     * @param RoleModelFactory|null     $roleModelFactory
     * @param MetaDataRepository|null   $metaDataRepository
     * @param MetaDataModelFactory|null $metaDataModelFactory
     *
     * @return RoleManager
     */
    private function getRoleManager(
        RoleRepository $roleRepository = null,
        RoleModelFactory $roleModelFactory = null,
        MetaDataRepository $metaDataRepository = null,
        MetaDataModelFactory $metaDataModelFactory = null
    ): RoleManager {
        return new RoleManager(
            $roleRepository ?: $this->createRoleRepository(),
            $roleModelFactory ?: $this->createRoleModelFactory(),
            $metaDataRepository ?: $this->createMetaDataRepository(),
            $metaDataModelFactory ?: $this->createMetaDataModelFactory()
        );
    }
}
