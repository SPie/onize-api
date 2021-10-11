<?php

namespace Tests\Unit\Projects;

use App\Models\Exceptions\ModelNotFoundException;
use App\Models\Exceptions\ModelsNotFoundException;
use App\Projects\MemberModelFactory;
use App\Projects\MemberRepository;
use App\Projects\PermissionRepository;
use App\Projects\RoleManager;
use App\Projects\RoleModelFactory;
use App\Projects\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class RoleManagerTest extends TestCase
{
    use ModelHelper;
    use ProjectHelper;
    use UsersHelper;

    private function getRoleManager(
        RoleRepository $roleRepository = null,
        RoleModelFactory $roleModelFactory = null,
        MemberRepository $memberRepository = null,
        MemberModelFactory $memberModelFactory = null,
        PermissionRepository $permissionRepository = null
    ): RoleManager {
        return new RoleManager(
            $roleRepository ?: $this->createRoleRepository(),
            $roleModelFactory ?: $this->createRoleModelFactory(),
            $memberRepository ?: $this->createMemberRepository(),
            $memberModelFactory ?: $this->createMemberModelFactory(),
            $permissionRepository ?: $this->createPermissionRepository()
        );
    }

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

    public function testHasPermissionForAction(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $project, $user, $permissionName] = $this->setUpHasPermissionForActionTest();

        $this->assertTrue($roleManager->hasPermissionForAction($project, $user, $permissionName));
    }

    public function testHasPermissionForActionWithoutPermission(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $project, $user, $permissionName] = $this->setUpHasPermissionForActionTest(false);

        $this->assertFalse($roleManager->hasPermissionForAction($project, $user, $permissionName));
    }

    public function testHasPermissionForActionWithoutRole(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $project, $user, $permissionName] = $this->setUpHasPermissionForActionTest(true, false);

        $this->assertFalse($roleManager->hasPermissionForAction($project, $user, $permissionName));
    }

    public function testHasPermissionForActionWithOwner(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $project, $user, $permissionName] = $this->setUpHasPermissionForActionTest(false, true, true);

        $this->assertTrue($roleManager->hasPermissionForAction($project, $user, $permissionName));
    }

    private function setUpGetRoleTest(bool $withRole = true): array
    {
        $uuid = $this->getFaker()->uuid;
        $role = $this->createRoleModel();
        $roleRepository = $this->createRoleRepository();
        $this->mockRoleRepositoryFindOneByUuid($roleRepository, $withRole ? $role : null, $uuid);
        $roleManager = $this->getRoleManager($roleRepository);

        return [$roleManager, $uuid, $role];
    }

    public function testGetRole(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $uuid, $role] = $this->setUpGetRoleTest();

        $this->assertEquals($role, $roleManager->getRole($uuid));
    }

    public function testGetRoleWithoutRole(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $uuid] = $this->setUpGetRoleTest(false);

        $this->expectException(ModelNotFoundException::class);

        $roleManager->getRole($uuid);
    }

    private function setUpGetPermissionsTest(bool $withPermissions = true): array
    {
        $permissionName = $this->getFaker()->word;
        $permission = $this->createPermissionModel();
        $permissionRepository = $this->createPermissionRepository();
        $this->mockPermissionRepositoryFindByNames($permissionRepository, new ArrayCollection($withPermissions ? [$permission] : []), [$permissionName]);
        $roleManager = $this->getRoleManager(permissionRepository: $permissionRepository);

        return [$roleManager, $permissionName, $permission];
    }

    public function testGetPermissions(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $permissionName, $permission] = $this->setUpGetPermissionsTest();

        $this->assertEquals(new ArrayCollection([$permission]), $roleManager->getPermissions([$permissionName]));
    }

    public function testGetPermissionsWithoutFoundPermissions(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $permissionName] = $this->setUpGetPermissionsTest(false);

        try {
            $roleManager->getPermissions([$permissionName]);

            $this->assertTrue(false);
        } catch (ModelsNotFoundException $e) {
            $this->assertEquals([$permissionName], $e->getIdentifiers());
        }
    }

    private function setUpCreateRoleTest(): array
    {
        $project = $this->createProjectModel();
        $label = $this->getFaker()->word;
        $permission = $this->createPermissionModel();
        $role = $this->createRoleModel();
        $this->mockRoleModelSetPermissions($role, [$permission]);
        $roleModelFactory = $this->createRoleModelFactory();
        $this->mockRoleModelFactoryCreate($roleModelFactory, $role, $project, $label);
        $roleRepository = $this->createRoleRepository();
        $this->mockRepositorySave($roleRepository, $role);
        $roleManager = $this->getRoleManager($roleRepository, $roleModelFactory);

        return [$roleManager, $project, $label, $permission, $role, $roleRepository];
    }

    public function testCreateRole(): void
    {
        /** @var RoleManager $roleManager */
        [$roleManager, $project, $label, $permission, $role, $roleRepository] = $this->setUpCreateRoleTest();

        $this->assertEquals($role, $roleManager->createRole($project, $label, new ArrayCollection([$permission])));
        $this->assertRepositorySave($roleRepository, $role);
        $this->assertRoleModelSetPermissions($role, [$permission]);
    }
}
