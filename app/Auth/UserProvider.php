<?php

namespace App\Auth;

use App\Models\Exceptions\ModelNotFoundException;
use App\Models\PasswordHasher;
use App\Users\UserManager;
use App\Users\UserModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;

/**
 * Class UserProvider
 *
 * @package App\Auth
 */
class UserProvider implements UserProviderContract
{
    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * @var PasswordHasher
     */
    private PasswordHasher $passwordHasher;

    /**
     * UserProvider constructor.
     *
     * @param UserManager    $userManager
     * @param PasswordHasher $passwordHasher
     */
    public function __construct(UserManager $userManager, PasswordHasher $passwordHasher)
    {
        $this->userManager = $userManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @return UserManager
     */
    private function getUserManager(): UserManager
    {
        return $this->userManager;
    }

    /**
     * @return PasswordHasher
     */
    private function getPasswordHasher(): PasswordHasher
    {
        return $this->passwordHasher;
    }

    /**
     * @param int $identifier
     *
     * @return UserModel|Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        try {
            return $this->getUserManager()->getUserById($identifier);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * @param array $credentials
     *
     * @return UserModel|Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        try {
            return $this->getUserManager()->getUserByEmail($credentials[UserModel::PROPERTY_EMAIL]);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * @param Authenticatable $user
     * @param array           $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (empty($credentials[UserModel::PROPERTY_PASSWORD])) {
            return false;
        }

        return $this->getPasswordHasher()->check($credentials[UserModel::PROPERTY_PASSWORD], $user->getAuthPassword());
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
