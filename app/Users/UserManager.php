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

    /**
     * @param UserModel   $user
     * @param string|null $email
     *
     * @return UserModel
     */
    public function updateUserData(UserModel $user, ?string $email): UserModel
    {
        if (!empty($email)) {
            $user = $this->getUserRepository()->save($user->setEmail($email));
        }

        return $user;
    }

    /**
     * @param UserModel   $user
     * @param string|null $password
     *
     * @return UserModel
     */
    public function updatePassword(UserModel $user, ?string $password): UserModel
    {
        if (!empty($password)) {
            $user = $this->getUserRepository()->save(
                $this->getUserModelFactory()->setPassword($user, $password)
            );
        }

        return $user;
    }
}
