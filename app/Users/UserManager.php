<?php

namespace App\Users;

use App\Models\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

/**
 * Class UserManager
 *
 * @package App\Users
 */
class UserManager implements UserProvider
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var UserModelFactory
     */
    private UserModelFactory $userModelFactory;

    /**
     * UserManager constructor.
     *
     * @param UserRepository   $userRepository
     * @param UserModelFactory $userModelFactory
     */
    public function __construct(UserRepository $userRepository, UserModelFactory $userModelFactory)
    {
        $this->userRepository = $userRepository;
        $this->userModelFactory = $userModelFactory;
    }

    /**
     * @return UserRepository
     */
    private function getUserRepository(): UserRepository
    {
        return $this->userRepository;
    }

    /**
     * @return UserModelFactory
     */
    private function getUserModelFactory(): UserModelFactory
    {
        return $this->userModelFactory;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return UserModel
     */
    public function createUser(string $email, string $password): UserModel
    {
        return $this->getUserRepository()->save(
            $this->getUserModelFactory()->create($email, $password)
        );
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function isEmailUsed(string $email): bool
    {
        return !empty($this->getUserRepository()->findOneByEmail($email));
    }

    /**
     * @param mixed $identifier
     *
     * @return UserModel|Model|Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->getUserRepository()->find($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        // TODO: Implement retrieveByToken() method.
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // TODO: Implement updateRememberToken() method.
    }

    public function retrieveByCredentials(array $credentials)
    {
        // TODO: Implement retrieveByCredentials() method.
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // TODO: Implement validateCredentials() method.
    }
}
