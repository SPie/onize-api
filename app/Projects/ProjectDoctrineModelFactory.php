<?php

namespace App\Projects;

/**
 * Class ProjectDoctrineModelFactory
 *
 * @package App\Projects
 */
final class ProjectDoctrineModelFactory implements ProjectModelFactory
{
    public function create(string $label, string $description): ProjectModel
    {
        return new ProjectDoctrineModel($label, $description);
    }
}
