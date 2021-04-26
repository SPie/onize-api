<?php

namespace App\Projects;

use App\Models\Exceptions\ModelNotFoundException;
use App\Users\UserModel;

/**
 * Class RoleManager
 *
 * @package App\Projects
 */
class RoleManager
{
    /**
     * @var RoleRepository
     */
    private RoleRepository $roleRepository;

    /**
     * @var RoleModelFactory
     */
    private RoleModelFactory $roleModelFactory;

    /**
     * @var MetaDataRepository
     */
    private MetaDataRepository $metaDataRepository;

    /**
     * @var MetaDataModelFactory
     */
    private MetaDataModelFactory $metaDataModelFactory;

    /**
     * RoleManager constructor.
     *
     * @param RoleRepository       $roleRepository
     * @param RoleModelFactory     $roleModelFactory
     * @param MetaDataRepository   $metaDataRepository
     * @param MetaDataModelFactory $metaDataModelFactory
     */
    public function __construct(
        RoleRepository $roleRepository,
        RoleModelFactory $roleModelFactory,
        MetaDataRepository $metaDataRepository,
        MetaDataModelFactory $metaDataModelFactory
    ) {
        $this->roleRepository = $roleRepository;
        $this->roleModelFactory = $roleModelFactory;
        $this->metaDataRepository = $metaDataRepository;
        $this->metaDataModelFactory = $metaDataModelFactory;
    }

    /**
     * @return RoleRepository
     */
    private function getRoleRepository(): RoleRepository
    {
        return $this->roleRepository;
    }

    /**
     * @return RoleModelFactory
     */
    private function getRoleModelFactory(): RoleModelFactory
    {
        return $this->roleModelFactory;
    }

    /**
     * @return MetaDataRepository
     */
    private function getMetaDataRepository(): MetaDataRepository
    {
        return $this->metaDataRepository;
    }

    /**
     * @return MetaDataModelFactory
     */
    private function getMetaDataModelFactory(): MetaDataModelFactory
    {
        return $this->metaDataModelFactory;
    }

    /**
     * @param string $uuid
     *
     * @return RoleModel
     */
    public function getRole(string $uuid): RoleModel
    {
        $role = $this->roleRepository->findOneByUuid($uuid);
        if (!$role) {
            throw new ModelNotFoundException();
        }

        return $role;
    }

    /**
     * @param ProjectModel $project
     * @param UserModel    $user
     * @param array        $metaData
     *
     * @return RoleModel
     */
    public function createOwnerRole(ProjectModel $project, UserModel $user, array $metaData): RoleModel
    {
        $role = $this->getRoleRepository()->save(
            $this->getRoleModelFactory()
                ->create($project, 'Owner', true)
                ->addUser($user)
        );

        foreach ($metaData as $name => $value) {
            $this->getMetaDataRepository()->save(
                $this->getMetaDataModelFactory()->create(
                    $project,
                    $user,
                    $name,
                    $value
                ),
                false
            );
        }

        $this->getMetaDataRepository()->flush();

        return $role;
    }

    /**
     * @param ProjectModel $project
     * @param UserModel    $user
     * @param string       $permission
     *
     * @return bool
     */
    public function hasPermissionForAction(ProjectModel $project, UserModel $user, string $permission): bool
    {
        $role = $user->getRoleForProject($project);

        return $role && ($role->isOwner() || $role->hasPermission($permission));
    }
}
