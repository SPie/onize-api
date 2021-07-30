<?php

namespace Tests\Unit\Projects;

use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\MemberModelFactory;
use App\Projects\MemberRepository;
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
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $member = $this->createMemberModel();
        $role = $this->createRoleModel();
        $this->mockRoleModelAddMember($role, $member);
        $roleModelFactory = $this->createRoleModelFactory();
        $this->mockRoleModelFactoryCreate($roleModelFactory, $role, $project, 'Owner', true);
        $roleRepository = $this->createRoleRepository();
        $this->mockRepositorySave($roleRepository, $role);
        $memberModelFactory = $this->createMemberModelFactory();
        $this->mockMemberModelFactoryCreate($memberModelFactory, $member, $user, $role, $metaData);
        $memberRepository = $this->createMemberRepository();
        $this->mockRepositorySave($memberRepository, $member);
        $roleManager = $this->getRoleManager(
            $roleRepository,
            $roleModelFactory,
            $memberRepository,
            $memberModelFactory
        );

        return [
            $roleManager,
            $project,
            $user,
            $metaData,
            $roleRepository,
            $role,
            $member
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
            $member
        ] = $this->setUpCreateOwnerRoleTest();

        $this->assertEquals($role, $roleManager->createOwnerRole($project, $user, $metaData));
        $this
            ->assertRoleModelAddMember($role, $member)
            ->assertRepositorySave($roleRepository, $role);
    }

    /**
     * @param bool $withPermission
     * @param bool $withRole
     * @param bool $withOwner
     *
     * @return array
     */
    private function setUpHasPermissionForActionTest(
        bool $withPermission = true,
        bool $withRole = true,
        bool $withOwner = false
    ): array {
        $permissionName = $this->getFaker()->word;
        $project = $this->createProjectModel();
        $role = $this->createRoleModel();
        $this
            ->mockRoleModelHasPermission($role, $withPermission, $permissionName)
            ->mockRoleModelIsOwner($role, $withOwner);
        $user = $this->createUserModel();
        $this->mockUserModelGetRoleForProject($user, $withRole ? $role : null, $project);
        $roleManager = $this->getRoleManager();

        return [$roleManager, $project, $user, $permissionName];
    }

    /**
     * @return void
     */
    public function testHasPermissionForAction(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $project, $user, $permissionName] = $this->setUpHasPermissionForActionTest();

        $this->assertTrue($roleManager->hasPermissionForAction($project, $user, $permissionName));
    }

    /**
     * @return void
     */
    public function testHasPermissionForActionWithoutPermission(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $project, $user, $permissionName] = $this->setUpHasPermissionForActionTest(false);

        $this->assertFalse($roleManager->hasPermissionForAction($project, $user, $permissionName));
    }

    /**
     * @return void
     */
    public function testHasPermissionForActionWithoutRole(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $project, $user, $permissionName] = $this->setUpHasPermissionForActionTest(true, false);

        $this->assertFalse($roleManager->hasPermissionForAction($project, $user, $permissionName));
    }

    /**
     * @return void
     */
    public function testHasPermissionForActionWithOwner(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $project, $user, $permissionName] = $this->setUpHasPermissionForActionTest(false, true, true);

        $this->assertTrue($roleManager->hasPermissionForAction($project, $user, $permissionName));
    }

    /**
     * @return array
     */
    private function setUpGetRoleTest(bool $withRole = true): array
    {
        $uuid = $this->getFaker()->uuid;
        $role = $this->createRoleModel();
        $roleRepository = $this->createRoleRepository();
        $this->mockRoleRepositoryFindOneByUuid($roleRepository, $withRole ? $role : null, $uuid);
        $roleManager = $this->getRoleManager($roleRepository);

        return [$roleManager, $uuid, $role];
    }

    /**
     * @return void
     */
    public function testGetRole(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $uuid, $role] = $this->setUpGetRoleTest();

        $this->assertEquals($role, $roleManager->getRole($uuid));
    }

    /**
     * @return void
     */
    public function testGetRoleWithoutRole(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $uuid] = $this->setUpGetRoleTest(false);

        $this->expectException(ModelNotFoundException::class);

        $roleManager->getRole($uuid);
    }

    //endregion

    /**
     * @param RoleRepository|null       $roleRepository
     * @param RoleModelFactory|null     $roleModelFactory
     * @param MemberRepository|null     $memberRepository
     * @param MemberModelFactory|null   $memberModelFactory
     *
     * @return RoleManager
     */
    private function getRoleManager(
        RoleRepository $roleRepository = null,
        RoleModelFactory $roleModelFactory = null,
        MemberRepository $memberRepository = null,
        MemberModelFactory $memberModelFactory = null
    ): RoleManager {
        return new RoleManager(
            $roleRepository ?: $this->createRoleRepository(),
            $roleModelFactory ?: $this->createRoleModelFactory(),
            $memberRepository ?: $this->createMemberRepository(),
            $memberModelFactory ?: $this->createMemberModelFactory()
        );
    }
}
