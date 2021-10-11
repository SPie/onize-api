<?php

namespace App\Http\Rules;

use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\ProjectModel;
use App\Users\UserManager;
use App\Users\UserModel;
use Illuminate\Contracts\Validation\Rule;

class UserExistsAndIsMember implements Rule
{
    private ?UserModel $user;

    private ?ProjectModel $project;

    public function __construct(private UserManager $userManager)
    {
        $this->user = null;
        $this->project = null;
    }

    public function setProject(ProjectModel $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getUser(): ?UserModel
    {
        return $this->user;
    }

    public function passes($attribute, $value)
    {
        try {
            $user = $this->userManager->getUserByUuid($value);
        } catch (ModelNotFoundException $e) {
            return false;
        }

        if ($this->project && !$user->isMemberOfProject($this->project)) {
            return false;
        }

        $this->user = $user;

        return true;
    }

    public function message()
    {
        return 'validation.user-not-found';
    }
}
