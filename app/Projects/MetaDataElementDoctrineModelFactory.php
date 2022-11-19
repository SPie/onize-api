<?php

namespace App\Projects;

final class MetaDataElementDoctrineModelFactory implements MetaDataElementModelFactory
{
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
