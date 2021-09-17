<?php

namespace App\Projects;

use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\Invites\Exceptions\UserNotMemberException;
use App\Users\UserModel;

class ProjectManager
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private ProjectModelFactory $projectModelFactory,
        private MetaDataElementModelFactory $metaDataElementModelFactory,
        private MemberRepository $memberRepository
    ) {
    }

    public function createProject(string $label, string $description, array $metaDataElements): ProjectModel
    {
        $project = $this->projectModelFactory->create($label, $description);

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
        foreach ($project->getMembers() as $member) {
            if ($member->getUser()->getId() === $user->getId()) {
                $this->memberRepository->delete($member);

                return $project;
            }
        }

        throw new UserNotMemberException(\sprintf('User %s is no member of project %s', $user->getUuid(), $project->getUuid()));
    }
}
