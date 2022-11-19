<?php

namespace App\Projects;

use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\Exceptions\UserIsNoMemberException;
use App\Users\UserModel;

class ProjectManager
{
    public function __construct(
        readonly private ProjectRepository $projectRepository,
        readonly private ProjectModelFactory $projectModelFactory,
        readonly private MetaDataElementModelFactory $metaDataElementModelFactory,
        readonly private MemberRepository $memberRepository,
        readonly private MemberModelFactory $memberModelFactory
    ) {
    }

    public function createProject(string $label, string $description, array $metaData, array $metaDataElements): ProjectModel
    {
        $project = $this->projectModelFactory->create($label, $description, $metaData);

        foreach ($metaDataElements as $metaDataElement) {
            $project->addMetaDataElement(
                $this->metaDataElementModelFactory->create(
                    $project,
                    $metaDataElement[MetaDataElementModel::PROPERTY_NAME],
                    $metaDataElement[MetaDataElementModel::PROPERTY_LABEL],
                    $metaDataElement[MetaDataElementModel::PROPERTY_TYPE],
                    $metaDataElement[MetaDataElementModel::PROPERTY_REQUIRED] ?? false,
                    $metaDataElement[MetaDataElementModel::PROPERTY_IN_LIST] ?? false
                )
            );
        }

        return $this->projectRepository->save($project);
    }

    public function getProject(string $uuid): ProjectModel
    {
        $project = $this->projectRepository->findOneByUuid($uuid);
        if (!$project) {
            throw new ModelNotFoundException(\sprintf('Project with uuid %s not found', $uuid));
        }

        return $project;
    }

    public function removeMember(ProjectModel $project, UserModel $user): ProjectModel
    {
        $member = $user->getMemberOfProject($project);
        if (!$member) {
            throw new UserIsNoMemberException(\sprintf('User %s is no member of project %s', $user->getUuid(), $project->getUuid()));
        }

        $this->memberRepository->delete($member);

        return $project;
    }

    public function changeRole(UserModel $user, RoleModel $role): UserModel
    {
        $member = $user->getMemberOfProject($role->getProject());
        if (!$member) {
            throw new UserIsNoMemberException(\sprintf('User %s is no member of project %s', $user->getUuid(), $role->getProject()->getUuid()));
        }

        $newMember = $this->memberModelFactory->create($user, $role, $member->getMetaData());

        $this->memberRepository->delete($member);
        $this->memberRepository->save($newMember);

        return $user->addMember($newMember);
    }
}
