<?php

namespace App\Http\Binders;

use App\Users\UserManager;
use App\Users\UserModel;

final class UserBinder
{
    public function __construct(private UserManager $userManager)
    {
    }

    public function bind(string $identifier): UserModel
    {
        return $this->userManager->getUserByUuid($identifier);
    }
}
