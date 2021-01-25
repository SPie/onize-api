<?php

namespace App\Http\Requests\Users;

use App\Auth\AuthManager;
use App\Http\Rules\UniqueUser;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Update
 *
 * @package App\Http\Requests\Users
 */
class Update extends FormRequest
{
    public const PARAMETER_EMAIL = 'email';
    /**
     * @var UniqueUser
     */
    private UniqueUser $uniqueUser;

    /**
     * @var AuthManager
     */
    private AuthManager $authManager;

    public function __construct(
        UniqueUser $uniqueUser,
        AuthManager $authManager,
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->uniqueUser = $uniqueUser;
        $this->authManager = $authManager;
    }

    /**
     * @return UniqueUser
     */
    private function getUniqueUser(): UniqueUser
    {
        return $this->uniqueUser;
    }

    /**
     * @return AuthManager
     */
    private function getAuthManager(): AuthManager
    {
        return $this->authManager;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::PARAMETER_EMAIL => [
                'email',
                $this->getUniqueUser()->setExistingUserId($this->getAuthManager()->authenticatedUser()->getId()),
            ]
        ];
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->get(self::PARAMETER_EMAIL);
    }
}
