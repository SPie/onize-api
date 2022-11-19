<?php

namespace App\Http\Requests\Projects;

use App\Http\Rules\PermissionsExist;
use App\Projects\ProjectModel;
use Doctrine\Common\Collections\Collection;
use Illuminate\Foundation\Http\FormRequest;

class CreateRole extends FormRequest
{
    private const PARAMETER_LABEL       = 'label';
    private const PARAMETER_PERMISSIONS = 'permissions';

    public function __construct(
        readonly private PermissionsExist $permissionsExist,
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
        return [
            self::PARAMETER_LABEL              => ['required', 'string'],
            self::PARAMETER_PERMISSIONS        => ['array', $this->permissionsExist],
            self::PARAMETER_PERMISSIONS . '.*' => ['distinct'],
        ];
    }

    public function getLabel(): string
    {
        return $this->get(self::PARAMETER_LABEL);
    }

    public function getPermissions(): Collection
    {
        return $this->permissionsExist->getPermissions();
    }
}
