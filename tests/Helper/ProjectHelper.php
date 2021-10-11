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

trait ProjectHelper
{
    /**
     * @return ProjectModel|MockInterface
     */
    private function createProjectModel(): ProjectModel
    {
        return m::spy(ProjectModel::class);
    }

    private function mockProjectModelAddRole(MockInterface $projectModel, RoleModel $role): self
    {
        $projectModel
            ->shouldReceive('addRole')
            ->with($role)
            ->andReturn($projectModel)
            ->once();

        return $this;
    }

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

    private function mockProjectModelToArray(MockInterface $projectModel, array $data): self
    {
        $projectModel
            ->shouldReceive('toarray')
            ->andReturn($data);

        return $this;
    }

    private function mockProjectModelGetMembers(MockInterface $projectModel, Collection $members): self
    {
        $projectModel
            ->shouldReceive('getMembers')
            ->andReturn($members);

        return $this;
    }

    private function mockProjectModelGetMetaData(MockInterface $projectModel, Collection $metaData): self
    {
        $projectModel
            ->shouldReceive('getMetaData')
            ->andReturn($metaData);

        return $this;
    }

    private function mockProjectModelHasMemberWithEmail(MockInterface $projectModel, bool $isMember, string $email): self
    {
        $projectModel
            ->shouldReceive('hasMemberWithEmail')
            ->with($email)
            ->andReturn($isMember);

        return $this;
    }

    private function mockProjectModelGetMetaDataElements(MockInterface $projectModel, Collection $metaDataElements): self
    {
        $projectModel
            ->shouldReceive('getMetaDataElements')
            ->andReturn($metaDataElements);

        return $this;
    }

    /**
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

    private function mockProjectManagerGetProject(MockInterface $projectManager, $project, string $uuid): self
    {
        $projectManager
            ->shouldReceive('getProject')
            ->with($uuid)
            ->andThrow($project);

        return $this;
    }

    private function assertProjectManagerRemoveMember(
        MockInterface $projectManager,
        ProjectModel $project,
        UserModel $user
    ): self {
        $projectManager
            ->shouldHaveReceived('removeMember')
            ->with($project, $user)
            ->once();

        return $this;
    }

    private function assertProjectManagerChangeRole(MockInterface $projectManager, UserModel $user, RoleModel $role): self
    {
        $projectManager
            ->shouldHaveReceived('changeRole')
            ->with($user, $role)
            ->once();

        return $this;
    }

    /**
     * @return ProjectRepository|MockInterface
     */
    private function createProjectRepository(): ProjectRepository
    {
        return m::spy(ProjectRepository::class);
    }

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

    private function mockRoleModelGetProject(MockInterface $roleModel, ProjectModel $project): self
    {
        $roleModel
            ->shouldReceive('getProject')
            ->andReturn($project);

        return $this;
    }

    private function mockRoleModelHasPermission(MockInterface $roleModel, bool $hasPermission, string $permissionName): self
    {
        $roleModel
            ->shouldReceive('hasPermission')
            ->with($permissionName)
            ->andReturn($hasPermission);

        return $this;
    }

    private function mockRoleModelIsOwner(MockInterface $roleModel, bool $owner): self
    {
        $roleModel
            ->shouldReceive('isOwner')
            ->andReturn($owner);

        return $this;
    }

    private function mockRoleModelGetMembers(MockInterface $roleModel, Collection $members): self
    {
        $roleModel
            ->shouldReceive('getMembers')
            ->andReturn($members);

        return $this;
    }

    private function mockRoleModelAddMember(MockInterface $roleModel, MemberModel $member): self
    {
        $roleModel
            ->shouldReceive('addMember')
            ->with($member)
            ->andReturn($roleModel);

        return $this;
    }

    private function assertRoleModelAddMember(MockInterface $roleModel, MemberModel $member): self
    {
        $roleModel
            ->shouldHaveReceived('addMember')
            ->with($member)
            ->once();

        return $this;
    }

    /**
     * @return RoleDoctrineModel[]|Collection
     */
    private function createRoleEntities(int $times = 1, array $attributes = []): Collection
    {
        return $this->createModelEntities(RoleDoctrineModel::class, $times, $attributes);
    }

    /**
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

    private function mockMetaDataElementModelToArray(MockInterface $metaDataElementModel, array $data): self
    {
        $metaDataElementModel
            ->shouldReceive('toArray')
            ->andReturn($data);

        return $this;
    }

    private function mockMetaDataElementModelGetName(MockInterface $metaDataElementModel, string $name): self
    {
        $metaDataElementModel
            ->shouldReceive('getName')
            ->andReturn($name);

        return $this;
    }

    private function mockMetaDataElementModelIsRequired(MockInterface $metaDataElementModel, bool $required): self
    {
        $metaDataElementModel
            ->shouldReceive('isRequired')
            ->andReturn($required);

        return $this;
    }

    private function mockMetaDataElementModelGetType(MockInterface $metaDataElementModel, string $type): self
    {
        $metaDataElementModel
            ->shouldReceive('getType')
            ->andReturn($type);

        return $this;
    }

    /**
     * @return MetaDataElementModelFactory|MockInterface
     */
    private function createMetaDataElementModelFactory(): MetaDataElementModelFactory
    {
        return m::spy(MetaDataElementModelFactory::class);
    }

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
     * @return PermissionModel|MockInterface
     */
    private function createPermissionModel(): PermissionModel
    {
        return m::spy(PermissionModel::class);
    }

    private function mockPermissionModelGetName(MockInterface $permissionModel, string $name): self
    {
        $permissionModel
            ->shouldReceive('getName')
            ->andReturn($name);

        return $this;
    }
    private function getConcretePermission(string $name): PermissionModel
    {
        return $this->app->get(PermissionRepository::class)->findOneBy([PermissionModel::PROPERTY_NAME => $name]);
    }

    private function getProjectsMembersShowPermission(): PermissionModel
    {
        return $this->getConcretePermission(PermissionModel::PERMISSION_PROJECTS_MEMBERS_SHOW);
    }

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

    private function mockInvitationModelToArray(MockInterface $invitationModel, array $data): self
    {
        $invitationModel
            ->shouldReceive('toArray')
            ->andReturn($data);

        return $this;
    }

    private function mockInvitationModelGetRole(MockInterface $invitationModel, RoleModel $role): self
    {
        $invitationModel
            ->shouldReceive('getRole')
            ->andReturn($role);

        return $this;
    }

    private function mockInvitationModelIsExpired(MockInterface $invitationModel, bool $expired): self
    {
        $invitationModel
            ->shouldReceive('isExpired')
            ->andReturn($expired);

        return $this;
    }

    private function mockInvitationModelGetAcceptedAt(MockInterface $invitationModel, ?\DateTimeImmutable $acceptedAt): self
    {
        $invitationModel
            ->shouldReceive('getAcceptedAt')
            ->andReturn($acceptedAt);

        return $this;
    }

    private function mockInvitationModelGetDeclinedAt(MockInterface $invitationModel, ?\DateTimeImmutable $declinedAt): self
    {
        $invitationModel
            ->shouldReceive('getDeclinedAt')
            ->andReturn($declinedAt);

        return $this;
    }

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

    private function mockMetaDataValidatorIsValidString(MockInterface $metaDataValidator, bool $valid, $value): self
    {
        $metaDataValidator
            ->shouldReceive('isValidString')
            ->with($value)
            ->andReturn($valid);

        return $this;
    }

    private function mockMetaDataValidatorIsValidEmail(MockInterface $metaDataValidator, bool $valid, $value): self
    {
        $metaDataValidator
            ->shouldReceive('isValidEmail')
            ->with($value)
            ->andReturn($valid);

        return $this;
    }

    private function mockMetaDataValidatorIsValidNumeric(MockInterface $metaDataValidator, bool $valid, $value): self
    {
        $metaDataValidator
            ->shouldReceive('isValidNumeric')
            ->with($value)
            ->andReturn($valid);

        return $this;
    }

    private function mockMetaDataValidatorIsValidDateTime(MockInterface $metaDataValidator, bool $valid, $value): self
    {
        $metaDataValidator
            ->shouldReceive('isValidDateTime')
            ->with($value)
            ->andReturn($valid);

        return $this;
    }

    /**
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

    private function mockMemberModelGetRole(MockInterface $memberModel, RoleModel $role): self
    {
        $memberModel
            ->shouldReceive('getRole')
            ->andReturn($role);

        return $this;
    }

    private function mockMemberModelGetMetaData(MockInterface $memberModel, array $metaData): self
    {
        $memberModel
            ->shouldReceive('getMetaData')
            ->andReturn($metaData);

        return $this;
    }

    private function mockMemberModelGetUser(MockInterface $memberModel, UserModel $user): self
    {
        $memberModel
            ->shouldReceive('getUser')
            ->andReturn($user);

        return $this;
    }

    /**
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
