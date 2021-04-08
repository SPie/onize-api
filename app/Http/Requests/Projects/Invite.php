<?php

namespace App\Http\Requests\Projects;

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

    /**
     * @return array
     */
    public function rules(): array
    {
        // TODO
    }

    /**
     * @return RoleModel
     */
    public function getRole(): RoleModel
    {
        // TODO
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        // TODO
    }

    /**
     * @return array
     */
    public function getMetaData(): array
    {
        // TODO
    }
}
