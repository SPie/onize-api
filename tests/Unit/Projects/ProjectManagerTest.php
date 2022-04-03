<?php

namespace Tests\Unit\Projects;

use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\Exceptions\UserIsNoMemberException;
use App\Projects\MemberModelFactory;
use App\Projects\MemberRepository;
use App\Projects\MetaDataElementModelFactory;
use App\Projects\ProjectManager;
use App\Projects\ProjectModelFactory;
use App\Projects\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class ProjectManagerTest extends TestCase
{
    use ModelHelper;
    use ProjectHelper;
    use UsersHelper;

    private function getProjectManager(
        ProjectRepository $projectRepository = null,
        ProjectModelFactory $projectModelFactory = null,
        MetaDataElementModelFactory $metaDataElementModelFactory = null,
        MemberRepository $memberRepository = null,
        MemberModelFactory $memberModelFactory = null
    ): ProjectManager {
        return new ProjectManager(
            $projectRepository ?: $this->createProjectRepository(),
            $projectModelFactory ?: $this->createProjectModelFactory(),
            $metaDataElementModelFactory ?: $this->createMetaDataElementModelFactory(),
            $memberRepository ?: $this->createMemberRepository(),
            $memberModelFactory ?: $this->createMemberModelFactory()
        );
    }

    private function setUpCreateProjectTest(bool $withOptionalMetaDataElementProperties = true): array
    {
        $name = $this->getFaker()->word;
        $description = $this->getFaker()->word;
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $metaDataElement = [
            'name'     => $this->getFaker()->word,
            'label'    => $this->getFaker()->word,
            'type'     => $this->createRandomMetaDataElementType(),
        ];
        if ($withOptionalMetaDataElementProperties) {
            $metaDataElement['required'] = $this->getFaker()->boolean;
            $metaDataElement['inList'] = $this->getFaker()->boolean;
        }
        $metaDataElements = [$metaDataElement];
        $project = $this->createProjectModel();
        $projectModelFactory = $this->createProjectModelFactory();
        $this->mockProjectModelFactoryCreate($projectModelFactory, $project, $name, $description, $metaData);
        $savedProject = $this->createProjectModel();
        $projectRepository = $this->createProjectRepository();
        $this->mockRepositorySave($projectRepository, $project, null, $savedProject);
        $metaDataElementModel = $this->createMetaDataElementModel();
        $metaDataElementModelFactory = $this->createMetaDataElementModelFactory();
        $this->mockMetaDataElementModelFactoryCreate(
            $metaDataElementModelFactory,
            $metaDataElementModel,
            $project,
            $metaDataElements[0]['name'],
            $metaDataElements[0]['label'],
            $metaDataElements[0]['type'],
            $withOptionalMetaDataElementProperties ? $metaDataElements[0]['required'] : false,
            $withOptionalMetaDataElementProperties ? $metaDataElements[0]['inList'] : false,
        );
        $this->mockProjectModelAddMetaDataElement($project, $metaDataElementModel);
        $projectManager = $this->getProjectManager(
            $projectRepository,
            $projectModelFactory,
            $metaDataElementModelFactory
        );

        return [
            $projectManager,
            $name,
            $description,
            $metaData,
            $metaDataElements,
            $savedProject
        ];
    }

    public function testCreateProject(): void
    {
        /** @var ProjectManager $projectManager */
        [$projectManager, $name, $description, $metaData, $metaDataElements, $project] = $this->setUpCreateProjectTest();

        $this->assertEquals($project, $projectManager->createProject($name, $description, $metaData, $metaDataElements));
    }

    public function testCreateProjectWithoutOptionalMetaDataElementProperties(): void
    {
        /** @var ProjectManager $projectManager */
        [$projectManager, $name, $description, $metaData, $metaDataElements, $project] = $this->setUpCreateProjectTest(withOptionalMetaDataElementProperties: false);

        $this->assertEquals($project, $projectManager->createProject($name, $description, $metaData, $metaDataElements));
    }

    private function setUpGetProjectTest(bool $withProject = true): array
    {
        $uuid = $this->getFaker()->uuid;
        $project = $this->createProjectModel();
        $projectRepository = $this->createProjectRepository();
        $this->mockProjectRepositoryFindOneByUuid($projectRepository, $withProject ? $project : null, $uuid);
        $projectManager = $this->getProjectManager($projectRepository);

        return [$projectManager, $uuid, $project];
    }

    public function testGetProject(): void
    {
        /** @var ProjectManager $projectManager */
        [$projectManager, $uuid, $project] = $this->setUpGetProjectTest();

        $this->assertEquals($project, $projectManager->getProject($uuid));
    }

    public function testGetProjectWithoutProject(): void
    {
        /** @var ProjectManager $projectManager */
        [$projectManager, $uuid] = $this->setUpGetProjectTest(false);

        $this->expectException(ModelNotFoundException::class);

        $projectManager->getProject($uuid);
    }

    private function setUpRemoveMemberTest(bool $userIsMember = true): array
    {
        $member = $this->createMemberModel();
        $project = $this->createProjectModel();
        $memberRepository = $this->createMemberRepository();
        $user = $this->createUserModel();
        $this->mockUserModelGetMemberOfProject($user, $userIsMember ? $member : null, $project);
        $projectManager = $this->getProjectManager(null, null, null, $memberRepository);

        return [$projectManager, $project, $user, $memberRepository, $member];
    }

    public function testRemoveMember(): void
    {
        /** @var ProjectManager $projectManager */
        [$projectManager, $project, $user, $memberRepository, $member] = $this->setUpRemoveMemberTest();

        $this->assertEquals($project, $projectManager->removeMember($project, $user));
        $this->assertRepositoryDelete($memberRepository, $member);
    }

    public function testRemoveMemberWithoutMemberFound(): void
    {
        /** @var ProjectManager $projectManager */
        [$projectManager, $project, $user] = $this->setUpRemoveMemberTest(false);

        $this->expectException(UserIsNoMemberException::class);

        $projectManager->removeMember($project, $user);
    }

    private function setUpChangeRoleTest(bool $userIsMember = true): array
    {
        $project = $this->createProjectModel();
        $role = $this->createRoleModel();
        $this->mockRoleModelGetProject($role, $project);
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $member = $this->createMemberModel();
        $this->mockMemberModelGetMetaData($member, $metaData);
        $newMember = $this->createMemberModel();
        $user = $this->createUserModel();
        $this
            ->mockUserModelGetMemberOfProject($user, $userIsMember ? $member : null, $project)
            ->mockUserModelAddMember($user, $newMember);
        $memberModelFactory = $this->createMemberModelFactory();
        $this->mockMemberModelFactoryCreate($memberModelFactory, $newMember, $user, $role, $metaData);
        $memberRepository = $this->createMemberRepository();
        $projectManager = $this->getProjectManager(
            null,
            null,
            null,
            $memberRepository,
            $memberModelFactory
        );

        return [$projectManager, $user, $role, $memberRepository, $member, $newMember];
    }

    public function testChangeRole(): void
    {
        /** @var ProjectManager $projectManager */
        [$projectManager, $user, $role, $memberRepository, $member, $newMember] = $this->setUpChangeRoleTest();

        $this->assertEquals($user, $projectManager->changeRole($user, $role));
        $this->assertRepositoryDelete($memberRepository, $member);
        $this->assertRepositorySave($memberRepository, $newMember);
        $this->assertUserModelAddMember($user, $newMember);
    }

    public function testChangeRoleWithUserIsNoMemberOfProject(): void
    {
        /** @var ProjectManager $projectManager */
        [$projectManager, $user, $role] = $this->setUpChangeRoleTest(false);

        $this->expectException(UserIsNoMemberException::class);

        $projectManager->changeRole($user, $role);
    }
}
