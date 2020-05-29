<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Validators\UniqueUser;
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
    const PARAMETER_REMEMBER = 'remember';

    private UniqueUser $uniqueUser;

    public function __construct(
        UniqueUser $uniqueUser,
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
    }

    /**
     * @return UniqueUser
     */
    private function getUniqueUser(): UniqueUser
    {
        return $this->uniqueUser;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::PARAMETER_EMAIL    => ['required', 'email', $this->getUniqueUser()],
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

    /**
     * @return bool
     */
    public function shouldRemember(): bool
    {
        return $this->get(self::PARAMETER_REMEMBER);
    }
}
