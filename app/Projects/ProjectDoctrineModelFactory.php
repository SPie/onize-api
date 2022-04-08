<?php

namespace App\Projects;

final class ProjectDoctrineModelFactory implements ProjectModelFactory
{
    public function create(string $label, string $description, array $metaData = []): ProjectModel
    {
        return new ProjectDoctrineModel($label, $description, $metaData);
    }
}
