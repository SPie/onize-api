<?php

namespace App\Http\Requests\Users;

use App\Http\Rules\UniqueUser;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Register
 *
 * @package App\Http\Requests\Users
 */
class Register extends FormRequest
{
    public const PARAMETER_EMAIL    = 'email';
    public const PARAMETER_PASSWORD = 'password';

    /**
     * @var UniqueUser
     */
    private UniqueUser $uniqueUser;

    /**
     * Register constructor.
     *
     * @param UniqueUser $uniqueUser
     * @param array      $query
     * @param array      $request
     * @param array      $attributes
     * @param array      $cookies
     * @param array      $files
     * @param array      $server
     * @param null       $content
     */
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
}
