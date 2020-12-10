<?php

namespace App\Projects;

/**
 * Class MetaDataElementDoctrineModelFactory
 *
 * @package App\Projects
 */
final class MetaDataElementDoctrineModelFactory implements MetaDataElementModelFactory
{
    /**
     * @param ProjectModel $project
     * @param string       $name
     * @param string       $label
     * @param string       $type
     * @param bool         $required
     * @param bool         $inList
     *
     * @return MetaDataElementModel
     */
    public function create(
        ProjectModel $project,
        string $name,
        string $label,
        string $type,
        bool $required = false,
        bool $inList = false
    ): MetaDataElementModel {
        return new MetaDataElementDoctrineModel($project, $name, $label, $type, $required, $inList);
    }
}
