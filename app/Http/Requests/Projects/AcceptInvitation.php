<?php

namespace App\Http\Requests\Projects;

use App\Http\Rules\ValidMetaData;
use Illuminate\Foundation\Http\FormRequest;

class AcceptInvitation extends FormRequest
{
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
        $project = $this->route('invitation')->getRole()->getProject();
        $this->validMetaDataRule->setProject($project);

        return [self::PARAMETER_META_DATA => [$this->validMetaDataRule]];
    }

    public function getMetaData(): array
    {
        return $this->get(self::PARAMETER_META_DATA, []);
    }
}
