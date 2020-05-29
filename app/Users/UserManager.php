<?php

namespace App\Users;

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
        // TODO
    }
}
