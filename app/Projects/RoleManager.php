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
     * RoleManager constructor.
     *
     * @param RoleRepository     $roleRepository
     * @param RoleModelFactory   $roleModelFactory
     * @param MemberRepository   $memberRepository
     * @param MemberModelFactory $memberModelFactory
     */
    public function __construct(
        private RoleRepository $roleRepository,
        private RoleModelFactory $roleModelFactory,
        private MemberRepository $memberRepository,
        private MemberModelFactory $memberModelFactory
    ) {
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
        $role = $this->roleRepository->save(
            $this->roleModelFactory->create($project, RoleModel::LABEL_OWNER, true)
        );
        $member = $this->memberRepository->save($this->memberModelFactory->create($user, $role, $metaData));

        return $role->addMember($member);
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
