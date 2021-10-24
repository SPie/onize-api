<?php

namespace App\Projects;

use App\Models\Exceptions\ModelNotFoundException;
use App\Models\Exceptions\ModelsNotFoundException;
use App\Users\UserModel;
use Doctrine\Common\Collections\Collection;

class RoleManager
{
    public function __construct(
        private RoleRepository $roleRepository,
        private RoleModelFactory $roleModelFactory,
        private MemberRepository $memberRepository,
        private MemberModelFactory $memberModelFactory,
        private PermissionRepository $permissionRepository
    ) {
    }

    public function getRole(string $uuid): RoleModel
    {
        $role = $this->roleRepository->findOneByUuid($uuid);
        if (!$role) {
            throw new ModelNotFoundException();
        }

        return $role;
    }

    public function createOwnerRole(ProjectModel $project, UserModel $user, array $metaData): RoleModel
    {
        $role = $this->roleRepository->save(
            $this->roleModelFactory->create($project, RoleModel::LABEL_OWNER, true)
        );
        $member = $this->memberRepository->save($this->memberModelFactory->create($user, $role, $metaData));

        return $role->addMember($member);
    }

    public function hasPermissionForAction(ProjectModel $project, UserModel $user, string $permission): bool
    {
        $role = $user->getRoleForProject($project);

        return $role && ($role->isOwner() || $role->hasPermission($permission));
    }

    public function getPermissions(array $names): Collection
    {
        $permissions = $this->permissionRepository->findByNames($names);
        if (\count($names) != $permissions->count()) {
            throw new ModelsNotFoundException(
                'PermissionModel',
                \array_diff($names, $permissions->map(fn (PermissionModel $permission) => $permission->getName())->getValues())
            );
        }

        return $permissions;
    }

    public function createRole(ProjectModel $project, string $label, Collection $permissions): RoleModel
    {
        $role = $this->roleModelFactory->create($project, $label)
            ->setPermissions($permissions->getValues());

        return $this->roleRepository->save($role);
    }

    public function removeRole(RoleModel $role, RoleModel $newRole = null): ?RoleModel
    {
        if ($newRole) {
            foreach ($role->getMembers() as $member) {
                $newRole->addMember($member->setRole($newRole));
            }

            $role->setMembers([]);

            $this->roleRepository->save($newRole, false);
        }

        $this->roleRepository->delete($role);

        return $newRole;
    }
}
