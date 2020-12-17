<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePassword
 *
 * @package App\Http\Requests\Users
 */
class UpdatePassword extends FormRequest
{
    public const PARAMETER_PASSWORD = 'password';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::PARAMETER_PASSWORD => ['string'],
        ];
    }

    /**
     * @return string|null
     */
    public function getUserPassword(): ?string
    {
        return $this->get(self::PARAMETER_PASSWORD);
    }
}
