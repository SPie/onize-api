<?php

namespace App\Http\Binders;

use App\Projects\ProjectManager;
use App\Projects\ProjectModel;

/**
 * Class ProjectBinder
 *
 * @package App\Http\Binders
 */
final class ProjectBinder
{
    /**
     * @var ProjectManager
     */
    private ProjectManager $projectManager;

    /**
     * ProjectBinder constructor.
     *
     * @param ProjectManager $projectManager
     */
    public function __construct(ProjectManager $projectManager)
    {
        $this->projectManager = $projectManager;
    }

    /**
     * @return ProjectManager
     */
    private function getProjectManager(): ProjectManager
    {
        return $this->projectManager;
    }

    /**
     * @param string $identifier
     *
     * @return ProjectModel
     */
    public function bind(string $identifier): ProjectModel
    {
        return $this->getProjectManager()->getProject($identifier);
    }
}
