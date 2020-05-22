<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Register
 *
 * @package App\Http\Requests\Users
 */
class Register extends FormRequest
{
    const PARAMETER_EMAIL    = 'email';
    const PARAMETER_PASSWORD = 'password';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::PARAMETER_EMAIL    => ['required', 'email'],
            self::PARAMETER_PASSWORD => ['required', 'string'],
        ];
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->get(self::PARAMETER_EMAIL);
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->get(self::PARAMETER_PASSWORD);
    }
}
