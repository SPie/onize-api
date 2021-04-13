<?php

namespace App\Http\Rules;

use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\RoleManager;
use App\Projects\RoleModel;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class RoleExists
 *
 * @package App\Http\Rules
 */
class RoleExists implements Rule
{
    /**
     * @var RoleManager
     */
    private RoleManager $roleManager;

    /**
     * @var RoleModel|null
     */
    private ?RoleModel $role;

    /**
     * RoleExists constructor.
     *
     * @param RoleManager $roleManager
     */
    public function __construct(RoleManager $roleManager)
    {
        $this->roleManager = $roleManager;
        $this->role = null;
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $this->role = $this->roleManager->getRole($value);
        } catch (ModelNotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'validation.role-not-found';
    }

    /**
     * @return RoleModel|null
     */
    public function getRole(): ?RoleModel
    {
        return $this->role;
    }
}
