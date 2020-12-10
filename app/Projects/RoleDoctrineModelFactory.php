<?php

namespace App\Projects;

use App\Models\UuidGenerator;

/**
 * Class RoleDoctrineModelFactory
 *
 * @package App\Projects
 */
final class RoleDoctrineModelFactory implements RoleModelFactory
{
    /**
     * @var UuidGenerator
     */
    private UuidGenerator $uuidGenerator;

    /**
     * RoleDoctrineModelFactory constructor.
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
     * @param ProjectModel $project
     * @param string       $label
     * @param bool         $owner
     *
     * @return RoleModel
     */
    public function create(ProjectModel $project, string $label, bool $owner = false): RoleModel
    {
        return new RoleDoctrineModel($this->getUuidGenerator()->generate(), $project, $label, $owner);
    }
}
