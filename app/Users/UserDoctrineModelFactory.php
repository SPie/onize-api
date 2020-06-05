<?php

namespace App\Users;

use App\Models\PasswordHasher;
use App\Models\UuidGenerator;

/**
 * Class UserDoctrineModelFactory
 *
 * @package App\Users
 */
final class UserDoctrineModelFactory implements UserModelFactory
{
    /**
     * @var UuidGenerator
     */
    private UuidGenerator $uuidGenerator;
    /**
     * @var PasswordHasher
     */
    private PasswordHasher $passwordHasher;

    /**
     * UserDoctrineModelFactory constructor.
     *
     * @param UuidGenerator  $uuidGenerator
     * @param PasswordHasher $passwordHasher
     */
    public function __construct(UuidGenerator $uuidGenerator, PasswordHasher $passwordHasher)
    {
        $this->uuidGenerator = $uuidGenerator;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @return UuidGenerator
     */
    private function getUuidGenerator(): UuidGenerator
    {
        return $this->uuidGenerator;
    }

    /**
     * @return PasswordHasher
     */
    private function getPasswordHasher(): PasswordHasher
    {
        return $this->passwordHasher;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return UserModel
     */
    public function create(string $email, string $password): UserModel
    {
        return new UserDoctrineModel(
            $this->getUuidGenerator()->generate(),
            $email,
            $this->getPasswordHasher()->hash($password)
        );
    }
}
