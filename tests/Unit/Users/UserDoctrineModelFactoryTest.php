<?php

namespace Tests\Unit\Users;

use App\Models\PasswordHasher;
use App\Models\UuidGenerator;
use App\Users\UserDoctrineModel;
use App\Users\UserDoctrineModelFactory;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class UserDoctrineModelFactoryTest
 *
 * @package Tests\Unit\Users
 */
final class UserDoctrineModelFactoryTest extends TestCase
{
    use ModelHelper;
    use UsersHelper;

    //region Tests

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $uuid = $this->getFaker()->uuid;
        $email = $this->getFaker()->safeEmail;
        $password = $this->getFaker()->password;
        $hashedPassword = $this->getFaker()->sha256;
        $uuidGenerator = $this->createUuidGenerator($uuid);
        $passwordHasher = $this->createPasswordHasher();
        $this->mockPasswordHasherHash($passwordHasher, $hashedPassword, $password);

        $this->assertEquals(
            new UserDoctrineModel($uuid, $email, $hashedPassword),
            $this->getUserDoctrineModelFactory($uuidGenerator, $passwordHasher)->create($email, $password)
        );
    }

    /**
     * @return void
     */
    public function testSetPassword(): void
    {
        $password = $this->getFaker()->password;
        $hashedPassword = $this->getFaker()->sha256;
        $passwordHasher = $this->createPasswordHasher();
        $this->mockPasswordHasherHash($passwordHasher, $hashedPassword, $password);
        $user = $this->createUserModel();
        $this->mockUserModelSetPassword($user, $hashedPassword);

        $this->assertEquals(
            $user,
            $this->getUserDoctrineModelFactory(null, $passwordHasher)->setPassword($user, $password)
        );
        $this->assertUserModelSetPassword($user, $hashedPassword);
    }

    //endregion

    /**
     * @param UuidGenerator|null  $uuidGenerator
     * @param PasswordHasher|null $passwordHasher
     *
     * @return UserDoctrineModelFactory
     */
    private function getUserDoctrineModelFactory(
        UuidGenerator $uuidGenerator = null,
        PasswordHasher $passwordHasher = null
    ): UserDoctrineModelFactory {
        return new UserDoctrineModelFactory(
            $uuidGenerator ?: $this->createUuidGenerator(),
            $passwordHasher ?: $this->createPasswordHasher()
        );
    }
}
