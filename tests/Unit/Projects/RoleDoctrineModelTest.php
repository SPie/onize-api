<?php

namespace Tests\Unit\Projects;

use App\Projects\ProjectModel;
use App\Projects\RoleDoctrineModel;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class RoleDoctrineModelTest
 *
 * @package Tests\Unit\Projects
 */
final class RoleDoctrineModelTest extends TestCase
{
    use ProjectHelper;

    //region Tests

    public function testToArray(): void
    {
        $uuid = $this->getFaker()->uuid;
        $role = $this->getRoleDoctrineModel()->setUuid($uuid);

        $this->assertEquals(
            [
                'uuid'  => $uuid,
                'label' => $role->getLabel(),
                'owner' => false,
            ],
            $role->toArray()
        );
    }

    public function testToArrayWithProject(): void
    {
        $uuid = $this->getFaker()->uuid;
        $projectData = [$this->getFaker()->word => $this->getFaker()->word];
        $project = $this->createProjectModel();
        $this->mockProjectModelToArray($project, $projectData);
        $role = $this->getRoleDoctrineModel($project)->setUuid($uuid);

        $this->assertEquals(
            [
                'uuid'    => $uuid,
                'label'   => $role->getLabel(),
                'owner'   => false,
                'project' => $projectData,
            ],
            $role->toArray(true)
        );
    }

    private function setUpHasPermissionTest(bool $withPermission = true, bool $withPermissionName = true): array
    {
        $permissionName = $this->getFaker()->word;
        $permission = $this->createPermissionModel();
        $this->mockPermissionModelGetName($permission, $permissionName . ($withPermissionName ? '' : $this->getFaker()->word));
        $role = $this->getRoleDoctrineModel();
        if ($withPermission) {
            $role->addPermission($permission);
        }

        return [$role, $permissionName];
    }

    public function testHasPermission(): void
    {
        /** @var RoleDoctrineModel $role */
        [$role, $permissionName] = $this->setUpHasPermissionTest();

        $this->assertTrue($role->hasPermission($permissionName));
    }

    public function testHasPermissionWithoutPermission(): void
    {
        /** @var RoleDoctrineModel $role */
        [$role, $permissionName] = $this->setUpHasPermissionTest(false);

        $this->assertFalse($role->hasPermission($permissionName));
    }

    public function testHasPermissionWithoutPermissionName(): void
    {
        /** @var RoleDoctrineModel $role */
        [$role, $permissionName] = $this->setUpHasPermissionTest(true, false);

        $this->assertFalse($role->hasPermission($permissionName));
    }

    //endregion

    private function getRoleDoctrineModel(ProjectModel $project = null, string $label = null): RoleDoctrineModel
    {
        return new RoleDoctrineModel(
            $project ?: $this->createProjectModel(),
            $label ?: $this->getFaker()->word
        );
    }
}
