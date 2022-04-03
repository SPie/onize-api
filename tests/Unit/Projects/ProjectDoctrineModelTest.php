<?php

namespace Tests\Unit\Projects;

use App\Projects\ProjectDoctrineModel;
use App\Projects\ProjectModel;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class ProjectDoctrineModelTest extends TestCase
{
    use ProjectHelper;
    use UsersHelper;

    //region Tests

    public function testToArray(): void
    {
        $uuid = $this->getFaker()->uuid;
        $project = $this->getProjectDoctrineModel()->setUuid($uuid);

        $this->assertEquals(
            [
                'uuid'             => $uuid,
                'label'            => $project->getLabel(),
                'description'      => $project->getDescription(),
                'metaData'         => [],
                'metaDataElements' => [],
                'roles'            => [],
            ],
            $project->toArray()
        );
    }

    public function testToArrayWithMetaDataElementsAndRoles(): void
    {
        $uuid = $this->getFaker()->uuid;
        $metaDataElementModelData = [$this->getFaker()->word => $this->getFaker()];
        $metaDataElement = $this->createMetaDataElementModel();
        $this->mockMetaDataElementModelToArray($metaDataElement, $metaDataElementModelData);
        $roleData = [$this->getFaker()->word => $this->getFaker()->word];
        $role = $this->createRoleModel();
        $this->mockRoleModelToArray($role, $roleData);
        $project = $this->getProjectDoctrineModel()
            ->setUuid($uuid)
            ->addMetaDataElement($metaDataElement)
            ->addRole($role);

        $this->assertEquals(
            [
                'uuid'             => $uuid,
                'label'            => $project->getLabel(),
                'description'      => $project->getDescription(),
                'metaData'         => [],
                'metaDataElements' => [$metaDataElementModelData],
                'roles'            => [$roleData],
            ],
            $project->toArray()
        );
    }

    public function testToArrayWithMetaData(): void
    {
        $uuid = $this->getFaker()->uuid;
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $project = $this->getProjectDoctrineModel()
            ->setUuid($uuid)
            ->setMetaData($metaData);

        $this->assertEquals(
            [
                'uuid'             => $uuid,
                'label'            => $project->getLabel(),
                'description'      => $project->getDescription(),
                'metaData'         => $metaData,
                'metaDataElements' => [],
                'roles'            => [],
            ],
            $project->toArray()
        );
    }

    private function setUpGetMembersTest(bool $withRoles = true, bool $withUsers = true): array
    {
        $member = $this->createMemberModel();
        $role = $this->createRoleModel();
        $this->mockRoleModelGetMembers($role, new ArrayCollection($withUsers ? [$member] : []));
        $project = $this->getProjectDoctrineModel()->setRoles($withRoles ? [$role] : []);

        return [$project, $member];
    }

    public function testGetMembers(): void
    {
        /** @var ProjectModel $project */
        [$project, $member] = $this->setUpGetMembersTest();

        $this->assertEquals(new ArrayCollection([$member]), $project->getMembers());
    }

    public function testGetMembersWithoutRoles(): void
    {
        /** @var ProjectModel $project */
        [$project] = $this->setUpGetMembersTest(false);

        $this->assertTrue($project->getMembers()->isEmpty());
    }

    public function testGetMembersWithoutUsersOnRoles(): void
    {
        /** @var ProjectModel $project */
        [$project] = $this->setUpGetMembersTest(true, false);

        $this->assertTrue($project->getMembers()->isEmpty());
    }

    private function setUpHasMemberWithEmailTest(bool $withMember = true): array
    {
        $email = $this->getFaker()->safeEmail;
        $user = $this->createUserModel();
        $this->mockUserModelGetEmail($user, ($withMember ? '' : $this->getFaker()->word) . $email);
        $member = $this->createMemberModel();
        $this->mockMemberModelGetUser($member, $user);
        $role = $this->createRoleModel();
        $this->mockRoleModelGetMembers($role, new ArrayCollection($withMember ? [$member] : []));
        $projectModel = $this->getProjectDoctrineModel();
        $projectModel->addRole($role);

        return [$projectModel, $email];
    }

    public function testHasMemberWithEmailWithMember(): void
    {
        /** @var ProjectDoctrineModel $projectModel */
        [$projectModel, $email] = $this->setUpHasMemberWithEmailTest();

        $this->assertTrue($projectModel->hasMemberWithEmail($email));
    }

    public function testHasMemberWithEmailWithoutMember(): void
    {
        /** @var ProjectDoctrineModel $projectModel */
        [$projectModel, $email] = $this->setUpHasMemberWithEmailTest(false);

        $this->assertFalse($projectModel->hasMemberWithEmail($email));
    }

    //endregion

    private function getProjectDoctrineModel(
        string $label = null,
        string $description = null
    ): ProjectDoctrineModel {
        return new ProjectDoctrineModel(
            $label ?: $this->getFaker()->word,
            $description ?: $this->getFaker()->word
        );
    }
}
