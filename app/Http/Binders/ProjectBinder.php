<?php

namespace App\Http\Binders;

use App\Projects\ProjectManager;
use App\Projects\ProjectModel;

final class ProjectBinder
{
    public function __construct(private ProjectManager $projectManager)
    {
    }

    public function bind(string $identifier): ProjectModel
    {
        return $this->projectManager->getProject($identifier);
    }
}
