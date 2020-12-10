<?php

namespace App\Projects;

use App\Models\UuidGenerator;

/**
 * Class ProjectDoctrineModelFactory
 *
 * @package App\Projects
 */
final class ProjectDoctrineModelFactory implements ProjectModelFactory
{
    /**
     * @var UuidGenerator
     */
    private UuidGenerator $uuidGenerator;

    /**
     * ProjectDoctrineModelFactory constructor.
     *
     * @param UuidGenerator $uuidGenerator
     */
    public function __construct(UuidGenerator $uuidGenerator)
    {
        $this->uuidGenerator = $uuidGenerator;
    }

    /**
     * @return UuidGenerator
     */
    private function getUuidGenerator(): UuidGenerator
    {
        return $this->uuidGenerator;
    }

    /**
     * @param string $label
     * @param string $description
     *
     * @return ProjectModel
     */
    public function create(string $label, string $description): ProjectModel
    {
        return new ProjectDoctrineModel(
            $this->getUuidGenerator()->generate(),
            $label,
            $description
        );
    }
}
