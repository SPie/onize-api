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
     * @var ProjectRepository
     */
    private ProjectRepository $projectRepository;

    /**
     * @var ProjectModelFactory
     */
    private ProjectModelFactory $projectModelFactory;

    /**
     * @var MetaDataElementRepository
     */
    private MetaDataElementRepository $metaDataElementRepository;

    /**
     * @var MetaDataElementModelFactory
     */
    private MetaDataElementModelFactory $metaDataElementModelFactory;

    /**
     * ProjectManager constructor.
     *
     * @param ProjectRepository           $projectRepository
     * @param ProjectModelFactory         $projectModelFactory
     * @param MetaDataElementRepository   $metaDataElementRepository
     * @param MetaDataElementModelFactory $metaDataElementModelFactory
     */
    public function __construct(
        ProjectRepository $projectRepository,
        ProjectModelFactory $projectModelFactory,
        MetaDataElementRepository $metaDataElementRepository,
        MetaDataElementModelFactory $metaDataElementModelFactory
    ) {
        $this->projectRepository = $projectRepository;
        $this->projectModelFactory = $projectModelFactory;
        $this->metaDataElementRepository = $metaDataElementRepository;
        $this->metaDataElementModelFactory = $metaDataElementModelFactory;
    }

    /**
     * @return ProjectRepository
     */
    private function getProjectRepository(): ProjectRepository
    {
        return $this->projectRepository;
    }

    /**
     * @return ProjectModelFactory
     */
    private function getProjectModelFactory(): ProjectModelFactory
    {
        return $this->projectModelFactory;
    }

    /**
     * @return MetaDataElementRepository
     */
    private function getMetaDataElementRepository(): MetaDataElementRepository
    {
        return $this->metaDataElementRepository;
    }

    /**
     * @return MetaDataElementModelFactory
     */
    private function getMetaDataElementModelFactory(): MetaDataElementModelFactory
    {
        return $this->metaDataElementModelFactory;
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
        $project = $this->getProjectModelFactory()->create($label, $description);

        foreach ($metaDataElements as $metaDataElement) {
            $project->addMetaDataElement(
                $this->getMetaDataElementModelFactory()->create(
                    $project,
                    $metaDataElement[MetaDataElementModel::PROPERTY_NAME],
                    $metaDataElement[MetaDataElementModel::PROPERTY_LABEL],
                    $metaDataElement[MetaDataElementModel::PROPERTY_TYPE],
                    $metaDataElement[MetaDataElementModel::PROPERTY_REQUIRED] ?? false,
                    $metaDataElement[MetaDataElementModel::PROPERTY_IN_LIST] ?? false
                )
            );
        }

        return $this->getProjectRepository()->save($project);
    }

    /**
     * @param string $uuid
     *
     * @return ProjectModel
     */
    public function getProject(string $uuid): ProjectModel
    {
        $project = $this->getProjectRepository()->findOneByUuid($uuid);
        if (!$project) {
            throw new ModelNotFoundException(\sprintf('Project with uuid %s not found', $uuid));
        }

        return $project;
    }
}
