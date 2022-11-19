<?php

namespace App\Projects;

interface MetaDataElementModelFactory
{
    public function create(
        ProjectModel $project,
        string $name,
        string $label,
        string $type,
        bool $required = false,
        bool $inList = false
    ): MetaDataElementModel;
}
