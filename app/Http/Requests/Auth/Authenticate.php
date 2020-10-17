<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Authenticate
 *
 * @package App\Http\Requests\Auth
 */
class Authenticate extends FormRequest
{
    const PARAMETER_EMAIl    = 'email';
    const PARAMETER_PASSWORD = 'password';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::PARAMETER_EMAIl    => ['required'],
            self::PARAMETER_PASSWORD => ['required'],
        ];
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->get(self::PARAMETER_EMAIl);
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->get(self::PARAMETER_PASSWORD);
    }
}
