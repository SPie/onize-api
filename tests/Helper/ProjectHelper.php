<?php

namespace Tests\Helper;

use App\Projects\MetaDataElementModel;
use App\Projects\MetaDataElementModelFactory;
use App\Projects\MetaDataElementRepository;
use App\Projects\MetaDataModel;
use App\Projects\MetaDataModelFactory;
use App\Projects\MetaDataRepository;
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
     * @return MetaDataRepository|MockInterface
     */
    private function createMetaDataRepository(): MetaDataRepository
    {
        return m::spy(MetaDataRepository::class);
    }

    /**
     * @return MetaDataModelFactory|MockInterface
     */
    private function createMetaDataModelFactory(): MetaDataModelFactory
    {
        return m::spy(MetaDataModelFactory::class);
    }

    /**
     * @param MetaDataModelFactory|MockInterface $metaDataModelFactory
     * @param MetaDataModel                      $metaDataModel
     * @param ProjectModel                       $projectModel
     * @param UserModel                          $userModel
     * @param string                             $name
     * @param string                             $value
     *
     * @return $this
     */
    private function mockMetaDataModelFactoryCreate(
        MockInterface $metaDataModelFactory,
        MetaDataModel $metaDataModel,
        ProjectModel $projectModel,
        UserModel $userModel,
        string $name,
        string $value
    ): self {
        $metaDataModelFactory
            ->shouldReceive('create')
            ->with($projectModel, $userModel, $name, $value)
            ->andReturn($metaDataModel);

        return $this;
    }
}
