<?php

namespace App\Projects;

use App\Models\Exceptions\ModelNotFoundException;
use App\Models\Model;

/**
 * Class ProjectManager
 *
 * @package App\Projects
 */
class ProjectManager
{
    /**
     * ProjectManager constructor.
     *
     * @param ProjectRepository           $projectRepository
     * @param ProjectModelFactory         $projectModelFactory
     * @param MetaDataElementModelFactory $metaDataElementModelFactory
     */
    public function __construct(
        private ProjectRepository $projectRepository,
        private ProjectModelFactory $projectModelFactory,
        private MetaDataElementModelFactory $metaDataElementModelFactory
    ) {
    }

    /**
     * @param string $label
     * @param string $description
     * @param array  $metaDataElements
     *
     * @return ProjectModel|Model
     */
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

    /**
     * @param string $uuid
     *
     * @return ProjectModel
     */
    public function getProject(string $uuid): ProjectModel
    {
        $project = $this->projectRepository->findOneByUuid($uuid);
        if (!$project) {
            throw new ModelNotFoundException(\sprintf('Project with uuid %s not found', $uuid));
        }

        return $project;
    }
}
