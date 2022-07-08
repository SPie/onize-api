<?php

namespace App\Http\Requests\Users;

use App\Auth\AuthManager;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePassword extends FormRequest
{
    public const PARAMETER_PASSWORD         = 'password';
    public const PARAMETER_CURRENT_PASSWORD = 'currentPassword';

    public function __construct(
        private AuthManager $authManager,
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
            self::PARAMETER_PASSWORD         => ['required', 'string'],
            self::PARAMETER_CURRENT_PASSWORD => ['required', 'string', $this->getCorrectCurrentPasswordRule()],
        ];
    }

    public function getUserPassword(): string
    {
        return $this->get(self::PARAMETER_PASSWORD);
    }

    private function getCorrectCurrentPasswordRule(): \Closure
    {
        return function (string $argument, $currentPassword, \Closure $fail): bool {
            if (empty($currentPassword)) {
                // will be handled by other rule
                return true;
            }

            if ($this->authManager->validateCredentials($this->authManager->authenticatedUser(), $currentPassword)) {
                return true;
            }

            $fail('validation.invalid-password');

            return false;
        };
    }
}
