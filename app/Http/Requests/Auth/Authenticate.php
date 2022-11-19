<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class Authenticate extends FormRequest
{
    public const PARAMETER_EMAIl    = 'email';
    public const PARAMETER_PASSWORD = 'password';

    public function rules(): array
    {
        return [
            self::PARAMETER_EMAIl    => ['required'],
            self::PARAMETER_PASSWORD => ['required'],
        ];
    }

    public function getEmail(): string
    {
        return $this->get(self::PARAMETER_EMAIl);
    }

    public function getPassword(): string
    {
        return $this->get(self::PARAMETER_PASSWORD);
    }
}
