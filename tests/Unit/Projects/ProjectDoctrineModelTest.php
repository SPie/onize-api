<?php

namespace Tests\Unit\Projects;

use App\Projects\ProjectDoctrineModel;
use App\Projects\ProjectModel;
use App\Users\UserDoctrineModel;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class ProjectDoctrineModelTest
 *
 * @package Tests\Unit\Projects
 */
final class ProjectDoctrineModelTest extends TestCase
{
    use ProjectHelper;
    use UsersHelper;

    //region Tests

    /**
     * @return void
     */
    public function testToArray(): void
    {
        $project = $this->getProjectDoctrineModel();

        $this->assertEquals(
            [
                'uuid'             => $project->getUuid(),
                'label'            => $project->getLabel(),
                'description'      => $project->getDescription(),
                'metaDataElements' => [],
                'roles'            => [],
            ],
            $project->toArray()
        );
    }

    /**
     * @return void
     */
    public function testToArrayWithMetaDataElementsAndRoles(): void
    {
        $metaDataElementModelData = [$this->getFaker()->word => $this->getFaker()];
        $metaDataElement = $this->createMetaDataElementModel();
        $this->mockMetaDataElementModelToArray($metaDataElement, $metaDataElementModelData);
        $roleData = [$this->getFaker()->word => $this->getFaker()->word];
        $role = $this->createRoleModel();
        $this->mockRoleModelToArray($role, $roleData);
        $project = $this->getProjectDoctrineModel()
            ->addMetaDataElement($metaDataElement)
            ->addRole($role);

        $this->assertEquals(
            [
                'uuid'             => $project->getUuid(),
                'label'            => $project->getLabel(),
                'description'      => $project->getDescription(),
                'metaDataElements' => [$metaDataElementModelData],
                'roles'            => [$roleData],
            ],
            $project->toArray()
        );
    }

    /**
     * @param bool $withRoles
     * @param bool $withUsers
     *
     * @return array
     */
    private function setUpGetMembersTest(bool $withRoles = true, bool $withUsers = true): array
    {
        $member = $this->createUserModel();
        $role = $this->createRoleModel();
        $this->mockRoleModelGetUsers($role, new ArrayCollection($withUsers ? [$member] : []));
        $project = $this->getProjectDoctrineModel()->setRoles($withRoles ? [$role] : []);

        return [$project, $member];
    }

    /**
     * @return void
     */
    public function testGetMembers(): void
    {
        /** @var ProjectModel $project */
        [$project, $member] = $this->setUpGetMembersTest();

        $this->assertEquals(new ArrayCollection([$member]), $project->getMembers());
    }

    /**
     * @return void
     */
    public function testGetMembersWithoutRoles(): void
    {
        /** @var ProjectModel $project */
        [$project] = $this->setUpGetMembersTest(false);

        $this->assertTrue($project->getMembers()->isEmpty());
    }

    /**
     * @return void
     */
    public function testGetMembersWithoutUsersOnRoles(): void
    {
        /** @var ProjectModel $project */
        [$project] = $this->setUpGetMembersTest(true, false);

        $this->assertTrue($project->getMembers()->isEmpty());
    }

    /**
     * @param bool $withMember
     *
     * @return array
     */
    private function setUpHasMemberWithEmailTest(bool $withMember = true): array
    {
        $email = $this->getFaker()->safeEmail;
        $user = $this->createUserModel();
        $this->mockUserModelGetEmail($user, ($withMember ? '' : $this->getFaker()->word) . $email);
        $role = $this->createRoleModel();
        $this->mockRoleModelGetUsers($role, new ArrayCollection([$user]));
        $projectModel = $this->getProjectDoctrineModel();
        $projectModel->addRole($role);

        return [$projectModel, $email];
    }

    /**
     * @return void
     */
    public function testHasMemberWithEmailWithMember(): void
    {
        /** @var ProjectDoctrineModel $projectModel */
        [$projectModel, $email] = $this->setUpHasMemberWithEmailTest();

        $this->assertTrue($projectModel->hasMemberWithEmail($email));
    }

    /**
     * @return void
     */
    public function testHasMemberWithEmailWithoutMember(): void
    {
        /** @var ProjectDoctrineModel $projectModel */
        [$projectModel, $email] = $this->setUpHasMemberWithEmailTest(false);

        $this->assertFalse($projectModel->hasMemberWithEmail($email));
    }

    //endregion

    /**
     * @param string|null $uuid
     * @param string|null $label
     * @param string|null $description
     *
     * @return ProjectDoctrineModel
     */
    private function getProjectDoctrineModel(
        string $uuid = null,
        string $label = null,
        string $description = null
    ): ProjectDoctrineModel {
        return new ProjectDoctrineModel(
            $uuid ?: $this->getFaker()->uuid,
            $label ?: $this->getFaker()->word,
            $description ?: $this->getFaker()->word
        );
    }
}
