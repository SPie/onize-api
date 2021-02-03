<?php

namespace Tests\Unit\Users;

use App\Users\UserDoctrineModel;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class UserDoctrineModelTest
 *
 * @package Tests\Unit\Users
 */
final class UserDoctrineModelTest extends TestCase
{
    use ModelHelper;
    use ProjectHelper;

    //region Tests

    /**
     * @return void
     */
    public function testToarray(): void
    {
        $uuid = $this->getFaker()->uuid;
        $email = $this->getFaker()->safeEmail;

        $this->assertEquals(
            [
                'uuid' => $uuid,
                'email' => $email,
            ],
            $this->getUserDoctrineModel($uuid, $email)->toArray()
        );
    }

    /**
     * @return void
     */
    public function testGetAuthIdentifier(): void
    {
        $user = $this->getUserDoctrineModel()->setId($this->getFaker()->numberBetween());

        $this->assertEquals($user->getId(), $user->getAuthIdentifier());
    }

    /**
     * @return void
     */
    public function testGetAuthIdentifierName(): void
    {
        $this->assertEquals('id', $this->getUserDoctrineModel()->getAuthIdentifierName());
    }

    /**
     * @param bool $isMember
     * @param bool $withRoles
     *
     * @return array
     */
    private function setUpIsMemberOfProjectTest(bool $isMember = true, bool $withRoles = true): array
    {
        $project = $this->createProjectModel();
        $this->mockModelGetId($project, $this->getFaker()->numberBetween());
        $otherProject = $this->createProjectModel();
        $this->mockModelGetId($otherProject, $project->getId() + 1);
        $role = $this->createRoleModel();
        $this->mockRoleModelGetProject($role, $isMember ? $project : $otherProject);
        $user = $this->getUserDoctrineModel();
        if ($withRoles) {
            $user->addRole($role);
        }

        return [$user, $project];
    }

    /**
     * @return void
     */
    public function testIsMemberOfProjectWithMember(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project] = $this->setUpIsMemberOfProjectTest();

        $this->assertTrue($user->isMemberOfProject($project));
    }

    /**
     * @return void
     */
    public function testIsMemberOfProjectWithoutMember(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project] = $this->setUpIsMemberOfProjectTest(false);

        $this->assertFalse($user->isMemberOfProject($project));
    }

    /**
     * @return void
     */
    public function testIsMemberOfProjectWithoutRoles(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project] = $this->setUpIsMemberOfProjectTest(true, false);

        $this->assertFalse($user->isMemberOfProject($project));
    }

    /**
     * @return void
     */
    public function testMemberData(): void
    {
        $metaDataName = $this->getFaker()->word;
        $metaDataValue = $this->getFaker()->word;
        $metaData = $this->createMetaDataModel();
        $this
            ->mockMetaDataModelGetName($metaData, $metaDataName)
            ->mockMetaDataModelGetValue($metaData, $metaDataValue);
        $user = $this->getUserDoctrineModel()->setMetaData([$metaData]);

        $this->assertEquals(
            [
                'uuid' => $user->getUuid(),
                'email' => $user->getEmail(),
                'metaData' => [
                    $metaDataName => $metaDataValue
                ]
            ],
            $user->memberData()
        );
    }

    /**
     * @param bool $withRoles
     * @param bool $withRoleForProject
     *
     * @return array
     */
    private function setUpGetRoleForProjectTest(bool $withRoles = true, bool $withRoleForProject = true): array
    {
        $project = $this->createProjectModel();
        $this->mockModelGetId($project, $this->getFaker()->numberBetween());
        $otherProject = $this->createProjectModel();
        $this->mockModelGetId($otherProject, $project->getId() + 1);
        $role = $this->createRoleModel();
        $this->mockRoleModelGetProject($role, $withRoleForProject ? $project : $otherProject);
        $otherRole = $this->createRoleModel();
        $this->mockRoleModelGetProject($otherRole, $otherProject);
        $user = $this->getUserDoctrineModel();
        $user->setRoles($withRoles ? [$otherRole, $role] : []);

        return [$user, $project, $role];
    }

    /**
     * @return void
     */
    public function testGetRoleForProject(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project, $role] = $this->setUpGetRoleForProjectTest();

        $this->assertEquals($role, $user->getRoleForProject($project));
    }

    /**
     * @return void
     */
    public function testGetRoleForProjectWithoutRoles(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project] = $this->setUpGetRoleForProjectTest(false);

        $this->assertNull($user->getRoleForProject($project));
    }

    /**
     * @return void
     */
    public function testGetRoleForProjectWithoutRoleForProject(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project] = $this->setUpGetRoleForProjectTest(true, false);

        $this->assertNull($user->getRoleForProject($project));
    }

    //endregion

    /**
     * @param string|null $uuid
     * @param string|null $email
     * @param string|null $password
     *
     * @return UserDoctrineModel
     */
    private function getUserDoctrineModel(string $uuid = null, string $email = null, string $password = null): UserDoctrineModel
    {
        return new UserDoctrineModel(
            $uuid ?: $this->getFaker()->uuid,
            $email ?: $this->getFaker()->safeEmail,
            $password ?: $this->getFaker()->password
        );
    }
}
