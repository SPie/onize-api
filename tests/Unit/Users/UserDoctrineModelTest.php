<?php

namespace Tests\Unit\Users;

use App\Users\UserDoctrineModel;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

final class UserDoctrineModelTest extends TestCase
{
    use ModelHelper;
    use ProjectHelper;

    private function getUserDoctrineModel(string $email = null, string $password = null): UserDoctrineModel
    {
        return new UserDoctrineModel(
            $email ?: $this->getFaker()->safeEmail,
            $password ?: $this->getFaker()->password
        );
    }

    public function testToArray(): void
    {
        $uuid = $this->getFaker()->uuid;
        $email = $this->getFaker()->safeEmail;

        $this->assertEquals(
            [
                'uuid' => $uuid,
                'email' => $email,
            ],
            $this->getUserDoctrineModel($email)
                ->setUuid($uuid)
                ->toArray()
        );
    }

    public function testGetAuthIdentifier(): void
    {
        $user = $this->getUserDoctrineModel()->setId($this->getFaker()->numberBetween());

        $this->assertEquals($user->getId(), $user->getAuthIdentifier());
    }

    public function testGetAuthIdentifierName(): void
    {
        $this->assertEquals('id', $this->getUserDoctrineModel()->getAuthIdentifierName());
    }

    private function setUpIsMemberOfProjectTest(bool $isMember = true, bool $withMember = true): array
    {
        $project = $this->createProjectModel();
        $this->mockModelGetId($project, $this->getFaker()->numberBetween());
        $otherProject = $this->createProjectModel();
        $this->mockModelGetId($otherProject, $project->getId() + 1);
        $role = $this->createRoleModel();
        $this->mockRoleModelGetProject($role, $isMember ? $project : $otherProject);
        $member = $this->createMemberModel();
        $this->mockMemberModelGetRole($member, $role);
        $user = $this->getUserDoctrineModel();
        if ($withMember) {
            $user->addMember($member);
        }

        return [$user, $project];
    }

    public function testIsMemberOfProjectWithMember(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project] = $this->setUpIsMemberOfProjectTest();

        $this->assertTrue($user->isMemberOfProject($project));
    }

    public function testIsMemberOfProjectWithoutMember(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project] = $this->setUpIsMemberOfProjectTest(false);

        $this->assertFalse($user->isMemberOfProject($project));
    }

    public function testIsMemberOfProjectWithoutMembers(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project] = $this->setUpIsMemberOfProjectTest(true, false);

        $this->assertFalse($user->isMemberOfProject($project));
    }

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
        $member = $this->createMemberModel();
        $this->mockMemberModelGetRole($member, $role);
        $otherMember = $this->createMemberModel();
        $this->mockMemberModelGetRole($otherMember, $otherRole);
        $user = $this->getUserDoctrineModel();
        $user->setMembers($withRoles ? [$otherMember, $member] : []);

        return [$user, $project, $role];
    }

    public function testGetRoleForProject(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project, $role] = $this->setUpGetRoleForProjectTest();

        $this->assertEquals($role, $user->getRoleForProject($project));
    }

    public function testGetRoleForProjectWithoutRoles(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project] = $this->setUpGetRoleForProjectTest(false);

        $this->assertNull($user->getRoleForProject($project));
    }

    public function testGetRoleForProjectWithoutRoleForProject(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project] = $this->setUpGetRoleForProjectTest(true, false);

        $this->assertNull($user->getRoleForProject($project));
    }

    private function setUpGetMemberOfProjectTest(bool $withMembers = true, bool $withMemberForProject = true): array
    {
        $memberProject = $this->createProjectModel();
        $this->mockModelGetId($memberProject, $this->getFaker()->numberBetween(1));
        $role = $this->createRoleModel();
        $this->mockRoleModelGetProject($role, $memberProject);
        $member = $this->createMemberModel();
        $this->mockMemberModelGetRole($member, $role);
        $project = $this->createProjectModel();
        $this->mockModelGetId($project, $memberProject->getId() + ($withMemberForProject ? 0 : 1));
        $user = $this->getUserDoctrineModel();
        if ($withMembers) {
            $user->addMember($member);
        }

        return [$user, $project, $member];
    }

    public function testGetMemberOfProject(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project, $member] = $this->setUpGetMemberOfProjectTest();

        $this->assertEquals($member, $user->getMemberOfProject($project));
    }

    public function testGetMemberOfProjectWithoutMembers(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project] = $this->setUpGetMemberOfProjectTest(withMembers: false);

        $this->assertNull($user->getMemberOfProject($project));
    }

    public function testGetMemberOfProjectWithoutMemberForProject(): void
    {
        /** @var UserDoctrineModel $user */
        [$user, $project] = $this->setUpGetMemberOfProjectTest(withMemberForProject: false);

        $this->assertNull($user->getMemberOfProject($project));
    }
}
