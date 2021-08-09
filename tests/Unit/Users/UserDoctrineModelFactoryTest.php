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

    public function testCreate(): void
    {
        $email = $this->getFaker()->safeEmail;
        $password = $this->getFaker()->password;
        $hashedPassword = $this->getFaker()->sha256;
        $passwordHasher = $this->createPasswordHasher();
        $this->mockPasswordHasherHash($passwordHasher, $hashedPassword, $password);

        $this->assertEquals(
            new UserDoctrineModel($email, $hashedPassword),
            $this->getUserDoctrineModelFactory($passwordHasher)->create($email, $password)
        );
    }

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
            $this->getUserDoctrineModelFactory($passwordHasher)->setPassword($user, $password)
        );
        $this->assertUserModelSetPassword($user, $hashedPassword);
    }

    //endregion

    private function getUserDoctrineModelFactory(PasswordHasher $passwordHasher = null): UserDoctrineModelFactory
    {
        return new UserDoctrineModelFactory($passwordHasher ?: $this->createPasswordHasher());
    }
}
