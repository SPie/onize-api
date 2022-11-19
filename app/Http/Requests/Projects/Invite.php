<?php

namespace App\Http\Requests\Projects;

use App\Http\Rules\RoleExists;
use App\Http\Rules\ValidMetaData;
use App\Projects\ProjectModel;
use App\Projects\RoleModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;

class Invite extends FormRequest
{
    private const PARAMETER_ROLE      = 'role';
    private const PARAMETER_EMAIL     = 'email';
    private const PARAMETER_META_DATA = 'metaData';

    public function __construct(
        readonly private ValidMetaData $validMetaDataRule,
        readonly private RoleExists $roleExistsRule,
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

    private function getProject(): ProjectModel|Route
    {
        return $this->route('project');
    }

    public function rules(): array
    {
        $project = $this->getProject();
        $this->validMetaDataRule->setProject($project);
        $this->roleExistsRule->setProject($project);

        return [
            self::PARAMETER_ROLE      => ['required', $this->roleExistsRule],
            self::PARAMETER_EMAIL     => ['required', 'email'],
            self::PARAMETER_META_DATA => [$this->validMetaDataRule],
        ];
    }

    public function getRole(): RoleModel
    {
        return $this->roleExistsRule->getRole();
    }

    public function getEmail(): string
    {
        return $this->get(self::PARAMETER_EMAIL);
    }

    public function getMetaData(): array
    {
        return $this->get(self::PARAMETER_META_DATA, []);
    }
}
