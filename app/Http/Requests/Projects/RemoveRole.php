<?php

namespace App\Http\Requests\Projects;

use App\Http\Rules\RoleExists;
use App\Projects\RoleModel;
use Illuminate\Foundation\Http\FormRequest;

class RemoveRole extends FormRequest
{
    private const PARAMETER_NEW_ROLE = 'newRole';

    public function __construct(
        private RoleExists $roleExists,
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
        $currentRole = $this->route('role');
        $this->roleExists->setProject($currentRole->getProject());

        return [self::PARAMETER_NEW_ROLE => [$this->roleExists]];
    }

    public function getNewRole(): ?RoleModel
    {
        return $this->roleExists->getRole();
    }
}
