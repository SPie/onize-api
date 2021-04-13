<?php

namespace App\Http\Requests\Projects;

use App\Http\Rules\RoleExists;
use App\Projects\RoleModel;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Invite
 *
 * @package App\Http\Requests\Projects
 */
class Invite extends FormRequest
{
    private const PARAMETER_ROLE = 'role';
    private const PARAMETER_EMAIL = 'email';
    private const PARAMETER_META_DATA = 'metaData';
    private RoleExists $roleExists;

    /**
     * Invite constructor.
     *
     * @param RoleExists $roleExists
     * @param array      $query
     * @param array      $request
     * @param array      $attributes
     * @param array      $cookies
     * @param array      $files
     * @param array      $server
     * @param null       $content
     */
    public function __construct(
        RoleExists $roleExists,
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->roleExists = $roleExists;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::PARAMETER_ROLE      => ['required', $this->roleExists],
            self::PARAMETER_EMAIL     => ['required', 'email'],
            self::PARAMETER_META_DATA => ['array'],
        ];
    }

    /**
     * @return RoleModel
     */
    public function getRole(): RoleModel
    {
        return $this->roleExists->getRole();
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->get(self::PARAMETER_EMAIL);
    }

    /**
     * @return array
     */
    public function getMetaData(): array
    {
        return $this->get(self::PARAMETER_META_DATA, []);
    }
}
