<?php

namespace App\Users;

use App\Models\Exceptions\ModelNotFoundException;

class UserManager
{
    public function __construct(private UserRepository $userRepository, private UserModelFactory $userModelFactory)
    {
    }

    public function createUser(string $email, string $password): UserModel
    {
        return $this->userRepository->save($this->userModelFactory->create($email, $password));
    }

    public function getUserById(int $id): UserModel
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new ModelNotFoundException(\sprintf('User with id %d not found.', $id));
        }

        return $user;
    }

    public function getUserByEmail(string $email): UserModel
    {
        $user = $this->userRepository->findOneByEmail($email);
        if (!$user) {
            throw new ModelNotFoundException(\sprintf('User with email %s not found.', $email));
        }

        return $user;
    }

    public function updateUserData(UserModel $user, ?string $email): UserModel
    {
        if (!empty($email)) {
            $user = $this->userRepository->save($user->setEmail($email));
        }

        return $user;
    }

    public function updatePassword(UserModel $user, ?string $password): UserModel
    {
        if (!empty($password)) {
            $user = $this->userRepository->save(
                $this->userModelFactory->setPassword($user, $password)
            );
        }

        return $user;
    }

    public function getUserByUuid(string $uuid): UserModel
    {
        $user = $this->userRepository->findOneByUuid($uuid);
        if (!$user) {
            throw new ModelNotFoundException(\sprintf('User with uuid %s not found.', $uuid));
        }

        return $user;
    }
}
