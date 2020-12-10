<?php

namespace App\Projects;

/**
 * Interface ProjectModelFactory
 *
 * @package App\Projects
 */
interface ProjectModelFactory
{
    /**
     * @param string $label
     * @param string $description
     *
     * @return ProjectModel
     */
    public function create(string $label, string $description): ProjectModel;
}
