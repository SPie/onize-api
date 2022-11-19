<?php

namespace App\Http\Rules;

use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\ProjectModel;
use App\Projects\RoleManager;
use App\Projects\RoleModel;
use Illuminate\Contracts\Validation\Rule;

class RoleExists implements Rule
{
    private ?RoleModel $role;

    private ?ProjectModel $project;

    public function __construct(readonly private RoleManager $roleManager)
    {
        $this->role = null;
        $this->project = null;
    }

    public function setProject(ProjectModel $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function passes($attribute, $value): bool
    {
        if (empty($value)) {
            return true;
        }

        try {
            $role = $this->roleManager->getRole($value);
        } catch (ModelNotFoundException $e) {
            return false;
        }

        if ($this->project && $this->project->getId() !== $role->getProject()->getId()) {
            return false;
        }

        $this->role = $role;

        return true;
    }

    public function message(): string
    {
        return 'validation.role-not-found';
    }

    public function getRole(): ?RoleModel
    {
        return $this->role;
    }
}
