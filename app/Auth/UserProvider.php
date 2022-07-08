<?php

namespace App\Auth;

use App\Models\Exceptions\ModelNotFoundException;
use App\Models\PasswordHasher;
use App\Users\UserManager;
use App\Users\UserModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;

class UserProvider implements UserProviderContract
{
    public function __construct(private UserManager $userManager, private PasswordHasher $passwordHasher)
    {
    }

    /**
     * @param int $identifier
     */
    public function retrieveById($identifier): UserModel|Authenticatable|null
    {
        try {
            return $this->userManager->getUserById($identifier);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    public function retrieveByCredentials(array $credentials): UserModel|Authenticatable|null
    {
        try {
            return $this->userManager->getUserByEmail($credentials[UserModel::PROPERTY_EMAIL]);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        if (empty($credentials[UserModel::PROPERTY_PASSWORD])) {
            return false;
        }

        return $this->passwordHasher->check($credentials[UserModel::PROPERTY_PASSWORD], $user->getAuthPassword());
    }

    public function retrieveByToken($identifier, $token)
    {
        // TODO: Implement retrieveByToken() method.
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // TODO: Implement updateRememberToken() method.
    }
}
