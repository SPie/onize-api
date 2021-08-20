<?php

namespace App\Http\Requests\Projects;

use App\Http\Rules\ValidMetaData;
use Illuminate\Foundation\Http\FormRequest;

class Invite extends FormRequest
{
    private const PARAMETER_EMAIL = 'email';
    private const PARAMETER_META_DATA = 'metaData';

    public function __construct(
        private ValidMetaData $validMetaDataRule,
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
        $this->validMetaDataRule->setProject($this->route('role')->getProject());

        return [
            self::PARAMETER_EMAIL     => ['required', 'email'],
            self::PARAMETER_META_DATA => [$this->validMetaDataRule],
        ];
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
