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

    public function __construct(private RoleManager $roleManager)
    {
        $this->role = null;
        $this->project = null;
    }

    public function setProject(ProjectModel $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
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

    /**
     * @return string
     */
    public function message()
    {
        return 'validation.role-not-found';
    }

    public function getRole(): ?RoleModel
    {
        return $this->role;
    }
}
