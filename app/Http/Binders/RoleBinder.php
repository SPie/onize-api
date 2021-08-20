<?php

namespace App\Http\Binders;

use App\Projects\RoleManager;
use App\Projects\RoleModel;

final class RoleBinder
{
    public function __construct(private RoleManager $roleManager)
    {
    }

    public function bind(string $identifier): RoleModel
    {
        return $this->roleManager->getRole($identifier);
    }
}
