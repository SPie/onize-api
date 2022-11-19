<?php

namespace App\Http\Requests\Users;

use App\Http\Rules\UniqueUser;
use Illuminate\Foundation\Http\FormRequest;

class Register extends FormRequest
{
    public const PARAMETER_EMAIL    = 'email';
    public const PARAMETER_PASSWORD = 'password';

    public function __construct(
        readonly private UniqueUser $uniqueUser,
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
            self::PARAMETER_EMAIL    => ['required', 'email', $this->uniqueUser],
            self::PARAMETER_PASSWORD => ['required', 'string'],
        ];
    }

    public function getEmail(): string
    {
        return $this->get(self::PARAMETER_EMAIL);
    }

    public function getPassword(): string
    {
        return $this->get(self::PARAMETER_PASSWORD);
    }
}
