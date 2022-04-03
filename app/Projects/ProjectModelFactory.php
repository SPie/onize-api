<?php

namespace App\Projects;

interface ProjectModelFactory
{
    public function create(string $label, string $description, array $metaData = []): ProjectModel;
}
