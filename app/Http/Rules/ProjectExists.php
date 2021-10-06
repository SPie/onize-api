<?php

namespace App\Http\Rules;

use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\ProjectManager;
use App\Projects\ProjectModel;
use Illuminate\Contracts\Validation\Rule;

class ProjectExists implements Rule
{
    private ?ProjectModel $project = null;

    public function __construct(private ProjectManager $projectManager)
    {
    }

    public function getProject(): ProjectModel
    {
        return $this->project;
    }

    public function passes($attribute, $value)
    {
        try {
            $this->project = $this->projectManager->getProject($value);
        } catch (ModelNotFoundException $e) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return 'validation.project-not-found';
    }
}
