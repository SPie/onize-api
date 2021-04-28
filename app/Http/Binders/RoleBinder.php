<?php

namespace App\Http\Binders;

use App\Projects\RoleManager;
use App\Projects\RoleModel;

/**
 * Class RoleBinder
 *
 * @package App\Http\Binders
 */
final class RoleBinder
{
    /**
     * @var RoleManager
     */
    private RoleManager $roleManager;

    /**
     * RoleBinder constructor.
     *
     * @param RoleManager $roleManager
     */
    public function __construct(RoleManager $roleManager)
    {
        $this->roleManager = $roleManager;
    }

    /**
     * @param string $identifier
     *
     * @return RoleModel
     */
    public function bind(string $identifier): RoleModel
    {
        return $this->roleManager->getRole($identifier);
    }
}
