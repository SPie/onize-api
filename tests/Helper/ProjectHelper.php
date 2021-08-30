<?php

namespace Tests\Helper;

use App\Projects\Invites\InvitationDoctrineModel;
use App\Projects\Invites\InvitationManager;
use App\Projects\Invites\InvitationModel;
use App\Projects\Invites\InvitationModelFactory;
use App\Projects\Invites\InvitationRepository;
use App\Projects\MemberDoctrineModel;
use App\Projects\MemberModel;
use App\Projects\MemberModelFactory;
use App\Projects\MemberRepository;
use App\Projects\MetaDataElementDoctrineModel;
use App\Projects\MetaDataElementModel;
use App\Projects\MetaDataElementModelFactory;
use App\Projects\MetaDataElementRepository;
use App\Projects\MetaData\MetaDataManager;
use App\Projects\MetaDataModel;
use App\Projects\MetaData\MetaDataValidator;
use App\Projects\PermissionDoctrineModel;
use App\Projects\PermissionModel;
use App\Projects\PermissionRepository;
use App\Projects\ProjectDoctrineModel;
use App\Projects\ProjectManager;
use App\Projects\ProjectModel;
use App\Projects\ProjectModelFactory;
use App\Projects\ProjectRepository;
use App\Projects\RoleDoctrineModel;
use App\Projects\RoleManager;
use App\Projects\RoleModel;
use App\Projects\RoleModelFactory;
use App\Projects\RoleRepository;
use App\Users\UserModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mockery as m;
use Mockery\MockInterface;

/**
 * Trait ProjectHelper
 *
 * @package Tests\Helper
 */
trait ProjectHelper
{
    /**
     * @return ProjectModel|MockInterface
     */
    private function createProjectModel(): ProjectModel
    {
        return m::spy(ProjectModel::class);
    }

    /**
     * @param ProjectModel|MockInterface $projectModel
     * @param RoleModel                  $role
     *
     * @return $this
     */
    private function mockProjectModelAddRole(MockInterface $projectModel, RoleModel $role): self
    {
        $projectModel
            ->shouldReceive('addRole')
            ->with($role)
            ->andReturn($projectModel)
            ->once();

        return $this;
    }

    /**
     * @param ProjectModel|MockInterface $projectModel
     * @param MetaDataElementModel       $metaDataElement
     *
     * @return $this
     */
    private function mockProjectModelAddMetaDataElement(
        MockInterface $projectModel,
        MetaDataElementModel $metaDataElement
    ): self {
        $projectModel
            ->shouldReceive('addMetaDataElement')
            ->with($metaDataElement)
            ->andReturn($projectModel)
            ->once();

        return $this;
    }

    /**
     * @param ProjectModel|MockInterface $projectModel
     * @param array                      $data
     *
     * @return $this
     */
    private function mockProjectModelToArray(MockInterface $projectModel, array $data): self
    {
        $projectModel
            ->shouldReceive('toarray')
            ->andReturn($data);

        return $this;
    }

    /**
     * @param ProjectModel|MockInterface $projectModel
     * @param MemberModel[]|Collection     $members
     *
     * @return $this
     */
    private function mockProjectModelGetMembers(MockInterface $projectModel, Collection $members): self
    {
        $projectModel
            ->shouldReceive('getMembers')
            ->andReturn($members);

        return $this;
    }

    /**
     * @param ProjectModel|MockInterface $projectModel
     * @param MetaDataModel[]|Collection $metaData
     *
     * @return $this
     */
    private function mockProjectModelGetMetaData(MockInterface $projectModel, Collection $metaData): self
    {
        $projectModel
            ->shouldReceive('getMetaData')
            ->andReturn($metaData);

        return $this;
    }

    /**
     * @param ProjectModel|MockInterface $projectModel
     * @param bool                       $isMember
     * @param string                     $email
     *
     * @return $this
     */
    private function mockProjectModelHasMemberWithEmail(MockInterface $projectModel, bool $isMember, string $email): self
    {
        $projectModel
            ->shouldReceive('hasMemberWithEmail')
            ->with($email)
            ->andReturn($isMember);

        return $this;
    }

    /**
     * @param ProjectModel|MockInterface        $projectModel
     * @param MetaDataElementModel[]|Collection $metaDataElements
     *
     * @return $this
     */
    private function mockProjectModelGetMetaDataElements(MockInterface $projectModel, Collection $metaDataElements): self
    {
        $projectModel
            ->shouldReceive('getMetaDataElements')
            ->andReturn($metaDataElements);

        return $this;
    }

    /**
     * @param int   $times
     * @param array $attributes
     *
     * @return ProjectDoctrineModel[]|Collection
     */
    private function createProjectEntities(int $times = 1, array $attributes = []): Collection
    {
        return $this->createModelEntities(ProjectDoctrineModel::class, $times, $attributes);
    }

    /**
     * @return ProjectManager|MockInterface
     */
    private function createProjectManager(): ProjectManager
    {
        return m::spy(ProjectManager::class);
    }

    /**
     * @param ProjectManager|MockInterface $projectManager
     * @param ProjectModel                 $project
     * @param string                       $name
     * @param string                       $description
     * @param array                        $metaDataElements
     *
     * @return $this
     */
    private function mockProjectManagerCreateProject(
        MockInterface $projectManager,
        ProjectModel $project,
        string $name,
        string $description,
        array $metaDataElements
    ): self {
        $projectManager
            ->shouldReceive('createProject')
            ->with($name, $description, $metaDataElements)
            ->andReturn($project);

        return $this;
    }

    /**
     * @param ProjectManager|MockInterface $projectManager
     * @param ProjectModel|\Exception      $project
     * @param string                       $uuid
     *
     * @return $this
     */
    private function mockProjectManagerGetProject(MockInterface $projectManager, $project, string $uuid): self
    {
        $projectManager
            ->shouldReceive('getProject')
            ->with($uuid)
            ->andThrow($project);

        return $this;
    }

    /**
     * @param ProjectManager|MockInterface $projectManager
     * @param UserModel[]|Collection       $members
     * @param ProjectModel                 $project
     *
     * @return $this
     */
    private function mockProjectManagerGetProjectMembers(
        MockInterface $projectManager,
        Collection $members,
        ProjectModel $project
    ): self {
        $projectManager
            ->shouldReceive('getProjectMembers')
            ->with($project)
            ->andReturn($members);

        return $this;
    }

    /**
     * @return ProjectRepository|MockInterface
     */
    private function createProjectRepository(): ProjectRepository
    {
        return m::spy(ProjectRepository::class);
    }

    /**
     * @param ProjectRepository|MockInterface $projectRepository
     * @param ProjectModel|null               $project
     * @param string                          $uuid
     *
     * @return $this
     */
    private function mockProjectRepositoryFindOneByUuid(
        MockInterface $projectRepository,
        ?ProjectModel $project,
        string $uuid
    ): self {
        $projectRepository
            ->shouldReceive('findOneByUuid')
            ->with($uuid)
            ->andReturn($project);

        return $this;
    }

    /**
     * @return ProjectModelFactory|MockInterface
     */
    private function createProjectModelFactory(): ProjectModelFactory
    {
        return m::spy(ProjectModelFactory::class);
    }

    /**
     * @param ProjectModelFactory|MockInterface $projectModelFactory
     * @param ProjectModel                      $projectModel
     * @param string                            $name
     * @param string                            $description
     *
     * @return $this
     */
    private function mockProjectModelFactoryCreate(
        MockInterface $projectModelFactory,
        ProjectModel $projectModel,
        string $name,
        string $description
    ): self {
        $projectModelFactory
            ->shouldReceive('create')
            ->with($name, $description)
            ->andReturn($projectModel);

        return $this;
    }

    /**
     * @return RoleModel|MockInterface
     */
    private function createRoleModel(): RoleModel
    {
        return m::spy(RoleModel::class);
    }

    /**
     * @param RoleModel|MockInterface $roleModel
     * @param UserModel               $userModel
     *
     * @return $this
     */
    private function mockRoleModelAddUser(MockInterface $roleModel, UserModel $userModel): self
    {
        $roleModel
            ->shouldReceive('addUser')
            ->with($userModel)
            ->andReturn($roleModel);

        return $this;
    }

    /**
     * @param RoleModel|MockInterface $roleModel
     * @param UserModel               $userModel
     *
     * @return $this
     */
    private function assertRoleModelAddUser(MockInterface $roleModel, UserModel $userModel): self
    {
        $roleModel
            ->shouldHaveReceived('addUser')
            ->with($userModel)
            ->once();

        return $this;
    }

    /**
     * @param RoleModel|MockInterface $roleModel
     * @param array                   $data
     * @param bool|null               $withProject
     *
     * @return $this
     */
    private function mockRoleModelToArray(MockInterface $roleModel, array $data, bool $withProject = null): self
    {
        $arguments = [];
        if ($withProject !== null) {
            $arguments[] = $withProject;
        }

        $roleModel
            ->shouldReceive('toArray')
            ->withArgs($arguments)
            ->andReturn($data);

        return $this;
    }

    /**
     * @param RoleModel|MockInterface $roleModel
     * @param ProjectModel            $project
     *
     * @return $this
     */
    private function mockRoleModelGetProject(MockInterface $roleModel, ProjectModel $project): self
    {
        $roleModel
            ->shouldReceive('getProject')
            ->andReturn($project);

        return $this;
    }

    /**
     * @param RoleModel|MockInterface $roleModel
     * @param bool                    $hasPermission
     * @param string                  $permissionName
     *
     * @return $this
     */
    private function mockRoleModelHasPermission(MockInterface $roleModel, bool $hasPermission, string $permissionName): self
    {
        $roleModel
            ->shouldReceive('hasPermission')
            ->with($permissionName)
            ->andReturn($hasPermission);

        return $this;
    }

    /**
     * @param RoleModel|MockInterface $roleModel
     * @param bool                    $owner
     *
     * @return $this
     */
    private function mockRoleModelIsOwner(MockInterface $roleModel, bool $owner): self
    {
        $roleModel
            ->shouldReceive('isOwner')
            ->andReturn($owner);

        return $this;
    }

    /**
     * @param RoleModel|MockInterface  $roleModel
     * @param MemberModel[]|Collection $members
     *
     * @return $this
     */
    private function mockRoleModelGetMembers(MockInterface $roleModel, Collection $members): self
    {
        $roleModel
            ->shouldReceive('getMembers')
            ->andReturn($members);

        return $this;
    }

    /**
     * @param RoleModel|MockInterface $roleModel
     * @param MemberModel             $member
     *
     * @return $this
     */
    private function mockRoleModelAddMember(MockInterface $roleModel, MemberModel $member): self
    {
        $roleModel
            ->shouldReceive('addMember')
            ->with($member)
            ->andReturn($roleModel);

        return $this;
    }

    /**
     * @param RoleModel|MockInterface $roleModel
     * @param MemberModel             $member
     *
     * @return $this
     */
    private function assertRoleModelAddMember(MockInterface $roleModel, MemberModel $member): self
    {
        $roleModel
            ->shouldHaveReceived('addMember')
            ->with($member)
            ->once();

        return $this;
    }

    /**
     * @param int   $times
     * @param array $attributes
     *
     * @return RoleDoctrineModel[]|Collection
     */
    private function createRoleEntities(int $times = 1, array $attributes = []): Collection
    {
        return $this->createModelEntities(RoleDoctrineModel::class, $times, $attributes);
    }

    /**
     * @param PermissionModel $permission
     *
     * @return RoleModel
     */
    private function createRoleWithPermission(PermissionModel $permission): RoleModel
    {
        return $this->createRoleEntities(1, [RoleModel::PROPERTY_PERMISSIONS => new ArrayCollection([$permission])])->first();
    }

    /**
     * @return RoleModel
     */
    private function createOwnerRole(): RoleModel
    {
        return $this->createRoleEntities(1, [RoleModel::PROPERTY_OWNER => true])->first();
    }

    /**
     * @return RoleManager|MockInterface
     */
    private function createRoleManager(): RoleManager
    {
        return m::spy(RoleManager::class);
    }

    /**
     * @param RoleManager|MockInterface $roleManager
     * @param RoleModel                 $role
     * @param ProjectModel              $project
     * @param UserModel                 $user
     * @param array                     $metaData
     *
     * @return $this
     */
    private function mockRoleManagerCreateOwnerRole(
        MockInterface $roleManager,
        RoleModel $role,
        ProjectModel $project,
        UserModel $user,
        array $metaData
    ): self {
        $roleManager
            ->shouldReceive('createOwnerRole')
            ->with($project, $user, $metaData)
            ->andReturn($role);

        return $this;
    }

    /**
     * @param RoleManager|MockInterface $roleManager
     * @param ProjectModel              $project
     * @param UserModel                 $user
     * @param array                     $metaData
     *
     * @return $this
     */
    private function assertRoleManagerCreateOwnerRole(
        MockInterface $roleManager,
        ProjectModel $project,
        UserModel $user,
        array $metaData
    ): self {
        $roleManager
            ->shouldHaveReceived('createOwnerRole')
            ->with($project, $user, $metaData)
            ->once();

        return $this;
    }

    /**
     * @param RoleManager|MockInterface $roleManager
     * @param bool                      $allowed
     * @param ProjectModel              $project
     * @param UserModel                 $user
     * @param string                    $permission
     *
     * @return $this
     */
    private function mockRoleManagerHasPermissionForAction(
        MockInterface $roleManager,
        bool $allowed,
        ProjectModel $project,
        UserModel $user,
        string $permission
    ): self {
        $roleManager
            ->shouldReceive('hasPermissionForAction')
            ->with($project, $user, $permission)
            ->andReturn($allowed);

        return $this;
    }

    /**
     * @param RoleManager|MockInterface $roleManager
     * @param RoleModel|\Exception      $role
     * @param string                    $uuid
     *
     * @return $this
     */
    private function mockRoleManagerGetRole(MockInterface $roleManager, $role, string $uuid): self
    {
        $roleManager
            ->shouldReceive('getRole')
            ->with($uuid)
            ->andThrow($role);

        return $this;
    }

    /**
     * @return MetaDataElementModel|MockInterface
     */
    private function createMetaDataElementModel(): MetaDataElementModel
    {
        return m::spy(MetaDataElementModel::class);
    }

    /**
     * @param MetaDataElementModel|MockInterface $metaDataElementModel
     * @param array                              $data
     *
     * @return $this
     */
    private function mockMetaDataElementModelToArray(MockInterface $metaDataElementModel, array $data): self
    {
        $metaDataElementModel
            ->shouldReceive('toArray')
            ->andReturn($data);

        return $this;
    }

    /**
     * @param MetaDataElementModel|MockInterface $metaDataElementModel
     * @param string                             $name
     *
     * @return $this
     */
    private function mockMetaDataElementModelGetName(MockInterface $metaDataElementModel, string $name): self
    {
        $metaDataElementModel
            ->shouldReceive('getName')
            ->andReturn($name);

        return $this;
    }

    /**
     * @param MetaDataElementModel|MockInterface $metaDataElementModel
     * @param bool                               $required
     *
     * @return $this
     */
    private function mockMetaDataElementModelIsRequired(MockInterface $metaDataElementModel, bool $required): self
    {
        $metaDataElementModel
            ->shouldReceive('isRequired')
            ->andReturn($required);

        return $this;
    }

    /**
     * @param MetaDataElementModel|MockInterface $metaDataElementModel
     * @param string                             $type
     *
     * @return $this
     */
    private function mockMetaDataElementModelGetType(MockInterface $metaDataElementModel, string $type): self
    {
        $metaDataElementModel
            ->shouldReceive('getType')
            ->andReturn($type);

        return $this;
    }

    /**
     * @return MetaDataElementRepository|MockInterface
     */
    private function createMetaDataElementRepository(): MetaDataElementRepository
    {
        return m::spy(MetaDataElementRepository::class);
    }

    /**
     * @return MetaDataElementModelFactory|MockInterface
     */
    private function createMetaDataElementModelFactory(): MetaDataElementModelFactory
    {
        return m::spy(MetaDataElementModelFactory::class);
    }

    /**
     * @param MetaDataElementModelFactory|MockInterface $metaDataElementModelFactory
     * @param MetaDataElementModel                      $metaDataElementModel
     * @param ProjectModel                              $project
     * @param string                                    $name
     * @param string                                    $label
     * @param string                                    $type
     * @param bool                                      $required
     * @param bool                                      $inList
     *
     * @return $this
     */
    private function mockMetaDataElementModelFactoryCreate(
        MockInterface $metaDataElementModelFactory,
        MetaDataElementModel $metaDataElementModel,
        ProjectModel $project,
        string $name,
        string $label,
        string $type,
        bool $required,
        bool $inList
    ): self {
        $metaDataElementModelFactory
            ->shouldReceive('create')
            ->with($project, $name, $label, $type, $required, $inList)
            ->andReturn($metaDataElementModel);

        return $this;
    }

    /**
     * @return string
     */
    private function createRandomMetaDataElementType(): string
    {
        $types = [
            'string',
            'email',
            'numeric',
            'date',
        ];

        return $types[\mt_rand(0, 3)];
    }

    /**
     * @return RoleRepository|MockInterface
     */
    private function createRoleRepository(): RoleRepository
    {
        return m::spy(RoleRepository::class);
    }

    /**
     * @param RoleRepository|MockInterface $roleRepository
     * @param RoleModel|null               $role
     * @param string                       $uuid
     *
     * @return $this
     */
    private function mockRoleRepositoryFindOneByUuid(MockInterface $roleRepository, ?RoleModel $role, string $uuid): self
    {
        $roleRepository
            ->shouldReceive('findOneByUuid')
            ->with($uuid)
            ->andReturn($role);

        return $this;
    }

    /**
     * @return RoleModelFactory|MockInterface
     */
    private function createRoleModelFactory(): RoleModelFactory
    {
        return m::spy(RoleModelFactory::class);
    }

    /**
     * @param RoleModelFactory|MockInterface $roleModelFactory
     * @param RoleModel                      $role
     * @param ProjectModel                   $project
     * @param string                         $label
     * @param bool|null                      $owner
     *
     * @return $this
     */
    private function mockRoleModelFactoryCreate(
        MockInterface $roleModelFactory,
        RoleModel $role,
        ProjectModel $project,
        string $label,
        bool $owner = null
    ): self {
        $arguments = [$project, $label];
        if ($owner !== null) {
            $arguments[] = $owner;
        }

        $roleModelFactory
            ->shouldReceive('create')
            ->withArgs($arguments)
            ->andReturn($role);

        return $this;
    }

    /**
     * @return MetaDataModel|MockInterface
     */
    private function createMetaDataModel(): MetaDataModel
    {
        return m::spy(MetaDataModel::class);
    }

    /**
     * @param MetaDataModel|MockInterface $metaDataModel
     * @param UserModel                   $user
     *
     * @return $this
     */
    private function mockMetaDataModelGetUser(MockInterface $metaDataModel, UserModel $user): self
    {
        $metaDataModel
            ->shouldReceive('getUser')
            ->andReturn($user);

        return $this;
    }

    /**
     * @param MetaDataModel|MockInterface $metaDataModel
     * @param string                      $name
     *
     * @return $this
     */
    private function mockMetaDataModelGetName(MockInterface $metaDataModel, string $name): self
    {
        $metaDataModel
            ->shouldReceive('getName')
            ->andReturn($name);

        return $this;
    }

    /**
     * @param MetaDataModel|MockInterface $metaDataModel
     * @param string                      $value
     *
     * @return $this
     */
    private function mockMetaDataModelGetValue(MockInterface $metaDataModel, string $value): self
    {
        $metaDataModel
            ->shouldReceive('getValue')
            ->andReturn($value);

        return $this;
    }

    /**
     * @return PermissionModel|MockInterface
     */
    private function createPermissionModel(): PermissionModel
    {
        return m::spy(PermissionModel::class);
    }

    /**
     * @param PermissionModel|MockInterface $permissionModel
     * @param string                        $name
     *
     * @return $this
     */
    private function mockPermissionModelGetName(MockInterface $permissionModel, string $name): self
    {
        $permissionModel
            ->shouldReceive('getName')
            ->andReturn($name);

        return $this;
    }

    /**
     * @param int   $times
     * @param array $attributes
     *
     * @return Collection
     */
    private function createPermissionEntities(int $times, array $attributes = []): Collection
    {
        return $this->createModelEntities(PermissionDoctrineModel::class, $times, $attributes);
    }

    /**
     * @param string $name
     *
     * @return PermissionModel
     */
    private function getConcretePermission(string $name): PermissionModel
    {
        return $this->app->get(PermissionRepository::class)->findOneBy([PermissionModel::PROPERTY_NAME => $name]);
    }

    /**
     * @return PermissionModel
     */
    private function getProjectsMembersShowPermission(): PermissionModel
    {
        return $this->getConcretePermission(PermissionModel::PERMISSION_PROJECTS_MEMBERS_SHOW);
    }

    /**
     * @return PermissionModel
     */
    private function getInvitationsManagementPermission(): PermissionModel
    {
        return $this->getConcretePermission(PermissionModel::PERMISSION_PROJECTS_INVITATIONS_MANAGEMENT);
    }

    /**
     * @return InvitationModel|MockInterface
     */
    private function createInvitationModel(): InvitationModel
    {
        return m::spy(InvitationModel::class);
    }

    /**
     * @param InvitationModel|MockInterface $invitationModel
     * @param array                         $data
     *
     * @return $this
     */
    private function mockInvitationModelToArray(MockInterface $invitationModel, array $data): self
    {
        $invitationModel
            ->shouldReceive('toArray')
            ->andReturn($data);

        return $this;
    }

    /**
     * @param InvitationModel|MockInterface $invitationModel
     * @param RoleModel                     $role
     *
     * @return $this
     */
    private function mockInvitationModelGetRole(MockInterface $invitationModel, RoleModel $role): self
    {
        $invitationModel
            ->shouldReceive('getRole')
            ->andReturn($role);

        return $this;
    }

    /**
     * @param InvitationModel|MockInterface $invitationModel
     * @param bool                          $expired
     *
     * @return $this
     */
    private function mockInvitationModelIsExpired(MockInterface $invitationModel, bool $expired): self
    {
        $invitationModel
            ->shouldReceive('isExpired')
            ->andReturn($expired);

        return $this;
    }

    /**
     * @param InvitationModel|MockInterface $invitationModel
     * @param \DateTimeImmutable|null       $acceptedAt
     *
     * @return $this
     */
    private function mockInvitationModelGetAcceptedAt(MockInterface $invitationModel, ?\DateTimeImmutable $acceptedAt): self
    {
        $invitationModel
            ->shouldReceive('getAcceptedAt')
            ->andReturn($acceptedAt);

        return $this;
    }

    /**
     * @param InvitationModel|MockInterface $invitationModel
     * @param \DateTimeImmutable|null       $declinedAt
     *
     * @return $this
     */
    private function mockInvitationModelGetDeclinedAt(MockInterface $invitationModel, ?\DateTimeImmutable $declinedAt): self
    {
        $invitationModel
            ->shouldReceive('getDeclinedAt')
            ->andReturn($declinedAt);

        return $this;
    }

    /**
     * @param InvitationModel|MockInterface $invitationModel
     * @param string                        $email
     *
     * @return $this
     */
    private function mockInvitationModelGetEmail(MockInterface $invitationModel, string $email): self
    {
        $invitationModel
            ->shouldReceive('getEmail')
            ->andReturn($email);

        return $this;
    }

    private function mockInvitationModelSetDeclinedAt(MockInterface $invitationModel, ?\DateTimeImmutable $declinedAt): self
    {
        $invitationModel
            ->shouldReceive('setDeclinedAt')
            ->with(m::on(fn (?\DateTimeImmutable $actual) => $actual == $declinedAt))
            ->andReturn($invitationModel);

        return $this;
    }

    private function assertInvitationModelSetDeclinedAt(MockInterface $invitationModel, ?\DateTimeImmutable $declinedAt): self
    {
        $invitationModel
            ->shouldHaveReceived('setDeclinedAt')
            ->with(m::on(fn (?\DateTimeImmutable $actual) => $actual == $declinedAt))
            ->once();

        return $this;
    }

    private function assertInvitationModelSetAcceptedAt(MockInterface $invitationModel, ?\DateTimeImmutable $acceptedAt): self
    {
        $invitationModel
            ->shouldHaveReceived('setAcceptedAt')
            ->with(m::on(fn (?\DateTimeImmutable $actual) => $actual == $acceptedAt))
            ->once();

        return $this;
    }

    /**
     * @return InvitationModelFactory|MockInterface
     */
    private function createInvitationModelFactory(): InvitationModelFactory
    {
        return m::spy(InvitationModelFactory::class);
    }

    /**
     * @param InvitationModelFactory|MockInterface $invitationModelFactory
     * @param InvitationModel                      $invitationModel
     * @param RoleModel                            $role
     * @param string                               $email
     * @param array                                $metaData
     *
     * @return $this
     */
    private function mockInvitationModelFactoryCreate(
        MockInterface $invitationModelFactory,
        InvitationModel $invitationModel,
        RoleModel $role,
        string $email,
        array $metaData
    ): self {
        $invitationModelFactory
            ->shouldReceive('create')
            ->with($role, $email, $metaData)
            ->andReturn($invitationModel);

        return $this;
    }

    /**
     * @return InvitationRepository|MockInterface
     */
    private function createInvitationRepository(): InvitationRepository
    {
        return m::spy(InvitationRepository::class);
    }

    /**
     * @param InvitationRepository|MockInterface $invitationRepository
     * @param InvitationModel|null               $invitation
     * @param string                             $uuid
     *
     * @return $this
     */
    private function mockInvitationRepositoryFindOneByUuid(
        MockInterface $invitationRepository,
        ?InvitationModel $invitation,
        string $uuid
    ): self {
        $invitationRepository
            ->shouldReceive('findOneByUuid')
            ->with($uuid)
            ->andReturn($invitation);

        return $this;
    }

    /**
     * @return InvitationManager|MockInterface
     */
    private function createInvitationManager(): InvitationManager
    {
        return m::spy(InvitationManager::class);
    }

    /**
     * @param InvitationManager|MockInterface $invitationManager
     * @param InvitationModel|\Exception      $invitation
     * @param RoleModel                       $role
     * @param string                          $email
     * @param array                           $metaData
     *
     * @return $this
     */
    private function mockInvitationManagerInviteMember(
        MockInterface $invitationManager,
        $invitation,
        RoleModel $role,
        string $email,
        array $metaData
    ): self {
        $invitationManager
            ->shouldReceive('inviteMember')
            ->with($role, $email, $metaData)
            ->andThrow($invitation);

        return $this;
    }

    /**
     * @param InvitationManager|MockInterface $invitationManager
     * @param InvitationModel                 $invitation
     * @param UserModel                       $user
     * @param array                           $metaData
     *
     * @return $this
     */
    private function assertInvitationManagerAcceptInvitation(
        MockInterface $invitationManager,
        InvitationModel $invitation,
        UserModel $user,
        array $metaData
    ): self {
        $invitationManager
            ->shouldHaveReceived('acceptInvitation')
            ->with($invitation, $user, $metaData)
            ->once();

        return $this;
    }

    /**
     * @param InvitationManager|MockInterface $invitationManager
     * @param InvitationModel|\Exception      $invitation
     * @param string                          $uuid
     *
     * @return $this
     */
    private function mockInvitationManagerGetInvitation(MockInterface $invitationManager, $invitation, string $uuid): self
    {
        $invitationManager
            ->shouldReceive('getInvitation')
            ->with($uuid)
            ->andThrow($invitation);

        return $this;
    }

    private function assertInvitationManagerDeclineInvitation(
        MockInterface $invitationManager,
        InvitationModel $invitation
    ): self {
        $invitationManager
            ->shouldHaveReceived('declineInvitation')
            ->with($invitation)
            ->once();

        return $this;
    }

    /**
     * @param int   $times
     * @param array $attributes
     *
     * @return InvitationModel[]|Collection
     */
    private function createInvitationEntities(int $times = 1, array $attributes = []): Collection
    {
        return $this->createModelEntities(InvitationDoctrineModel::class, $times, $attributes);
    }

    /**
     * @return MetaDataManager|MockInterface
     */
    private function createMetaDataManager(): MetaDataManager
    {
        return m::spy(MetaDataManager::class);
    }

    /**
     * @param MetaDataManager|MockInterface $metaDataManager
     * @param array                         $validationErrors
     * @param ProjectModel                  $project
     * @param array                         $metaData
     *
     * @return $this
     */
    private function mockMetaDataManagerValidateMetaData(
        MockInterface $metaDataManager,
        array $validationErrors,
        ProjectModel $project,
        array $metaData
    ): self {
        $metaDataManager
            ->shouldReceive('validateMetaData')
            ->with($project, $metaData)
            ->andReturn($validationErrors);

        return $this;
    }

    /**
     * @return MetaDataValidator|MockInterface
     */
    private function createMetaDataValidator(): MetaDataValidator
    {
        return m::spy(MetaDataValidator::class);
    }

    /**
     * @param MetaDataValidator|MockInterface $metaDataValidator
     * @param bool                            $valid
     * @param mixed                           $value
     *
     * @return $this
     */
    private function mockMetaDataValidatorIsValidString(MockInterface $metaDataValidator, bool $valid, $value): self
    {
        $metaDataValidator
            ->shouldReceive('isValidString')
            ->with($value)
            ->andReturn($valid);

        return $this;
    }

    /**
     * @param MetaDataValidator|MockInterface $metaDataValidator
     * @param bool                            $valid
     * @param mixed                           $value
     *
     * @return $this
     */
    private function mockMetaDataValidatorIsValidEmail(MockInterface $metaDataValidator, bool $valid, $value): self
    {
        $metaDataValidator
            ->shouldReceive('isValidEmail')
            ->with($value)
            ->andReturn($valid);

        return $this;
    }

    /**
     * @param MetaDataValidator|MockInterface $metaDataValidator
     * @param bool                            $valid
     * @param mixed                           $value
     *
     * @return $this
     */
    private function mockMetaDataValidatorIsValidNumeric(MockInterface $metaDataValidator, bool $valid, $value): self
    {
        $metaDataValidator
            ->shouldReceive('isValidNumeric')
            ->with($value)
            ->andReturn($valid);

        return $this;
    }

    /**
     * @param MetaDataValidator|MockInterface $metaDataValidator
     * @param bool                            $valid
     * @param mixed                           $value
     *
     * @return $this
     */
    private function mockMetaDataValidatorIsValidDateTime(MockInterface $metaDataValidator, bool $valid, $value): self
    {
        $metaDataValidator
            ->shouldReceive('isValidDateTime')
            ->with($value)
            ->andReturn($valid);

        return $this;
    }

    /**
     * @param int   $times
     * @param array $attributes
     *
     * @return MetaDataElementModel[]|Collection
     */
    private function createMetaDataElementEntities(int $times = 1, array $attributes = []): Collection
    {
        return $this->createModelEntities(MetaDataElementDoctrineModel::class, $times, $attributes);
    }

    /**
     * @return MemberModel|MockInterface
     */
    private function createMemberModel(): MemberModel
    {
        return m::spy(MemberModel::class);
    }

    /**
     * @param UserModel|MockInterface $memberModel
     * @param RoleModel               $role
     *
     * @return $this
     */
    private function mockMemberModelGetRole(MockInterface $memberModel, RoleModel $role): self
    {
        $memberModel
            ->shouldReceive('getRole')
            ->andReturn($role);

        return $this;
    }

    /**
     * @param MemberModel|MockInterface $memberModel
     * @param array                     $metaData
     *
     * @return $this
     */
    private function mockMemberModelGetMetaData(MockInterface $memberModel, array $metaData): self
    {
        $memberModel
            ->shouldReceive('getMetaData')
            ->andReturn($metaData);

        return $this;
    }

    /**
     * @param MemberModel|MockInterface $memberModel
     * @param UserModel                 $user
     *
     * @return $this
     */
    private function mockMemberModelGetUser(MockInterface $memberModel, UserModel $user): self
    {
        $memberModel
            ->shouldReceive('getUser')
            ->andReturn($user);

        return $this;
    }

    /**
     * @param int   $times
     * @param array $attributes
     *
     * @return MemberModel[]|Collection
     */
    private function createMemberEntities(int $times = 1, array $attributes = []): Collection
    {
        return $this->createModelEntities(MemberDoctrineModel::class, $times, $attributes);
    }

    /**
     * @return MemberModelFactory|MockInterface
     */
    private function createMemberModelFactory(): MemberModelFactory
    {
        return m::spy(MemberModelFactory::class);
    }

    /**
     * @param MemberModelFactory|MockInterface $memberModelFactory
     * @param MemberModel                      $member
     * @param UserModel                        $user
     * @param RoleModel                        $role
     * @param array                            $metaData
     *
     * @return $this
     */
    private function mockMemberModelFactoryCreate(
        MockInterface $memberModelFactory,
        MemberModel $member,
        UserModel $user,
        RoleModel $role,
        array $metaData
    ): self {
        $memberModelFactory
            ->shouldReceive('create')
            ->with($user, $role, $metaData)
            ->andReturn($member);

        return $this;
    }

    /**
     * @return MemberRepository|MockInterface
     */
    private function createMemberRepository(): MemberRepository
    {
        return m::spy(MemberRepository::class);
    }
}
