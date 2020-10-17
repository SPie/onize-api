<?php

namespace App\Users;

use App\Models\Exceptions\ModelNotFoundException;
use App\Models\Model;

/**
 * Class UserManager
 *
 * @package App\Users
 */
class UserManager
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
     * @param int $id
     *
     * @return UserModel|Model
     */
    public function getUserById(int $id): UserModel
    {
        $user = $this->getUserRepository()->find($id);
        if (!$user) {
            throw new ModelNotFoundException(\sprintf('User with id %d not found.', $id));
        }

        return $user;
    }

    /**
     * @param string $email
     *
     * @return UserModel
     */
    public function getUserByEmail(string $email): UserModel
    {
        $user = $this->getUserRepository()->findOneByEmail($email);
        if (!$user) {
            throw new ModelNotFoundException(\sprintf('User with email %s not found.', $email));
        }

        return $user;
    }
}
