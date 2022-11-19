<?php

namespace App\Http\Requests\Users;

use App\Auth\AuthManager;
use App\Http\Rules\UniqueUser;
use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public const PARAMETER_EMAIL = 'email';
    public function __construct(
        readonly private UniqueUser $uniqueUser,
        readonly private AuthManager $authManager,
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
            self::PARAMETER_EMAIL => [
                'email',
                $this->uniqueUser->setExistingUserId($this->authManager->authenticatedUser()->getId()),
            ]
        ];
    }

    public function getEmail(): ?string
    {
        return $this->get(self::PARAMETER_EMAIL);
    }
}
