<?php

namespace App\Http\Requests\Projects;

use App\Http\Rules\RoleExists;
use App\Http\Rules\UserExistsAndIsMember;
use App\Projects\ProjectModel;
use App\Projects\RoleModel;
use App\Users\UserModel;
use Illuminate\Foundation\Http\FormRequest;

class ChangeRole extends FormRequest
{
    private const PARAMETER_USER = 'user';
    private const PARAMETER_ROLE = 'role';

    public function __construct(
        private UserExistsAndIsMember $userExistsAndIsMemberRule,
        private RoleExists $roleExistsRule,
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    public function rules(): array
    {
        $project = $this->getProject();
        $this->userExistsAndIsMemberRule->setProject($project);
        $this->roleExistsRule->setProject($project);

        return [
            self::PARAMETER_USER => ['required', $this->userExistsAndIsMemberRule],
            self::PARAMETER_ROLE => ['required', $this->roleExistsRule],
        ];
    }

    public function getUser(): UserModel
    {
        return $this->userExistsAndIsMemberRule->getUser();
    }

    public function getRole(): RoleModel
    {
        return $this->roleExistsRule->getRole();
    }

    private function getProject(): ProjectModel
    {
        return $this->route('project');
    }
}
