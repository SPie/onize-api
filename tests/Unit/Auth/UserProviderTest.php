<?php

namespace Tests\Unit\Auth;

use App\Auth\UserProvider;
use App\Models\Exceptions\ModelNotFoundException;
use App\Models\PasswordHasher;
use App\Users\UserManager;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class UserProviderTest
 *
 * @package Tests\Unit\Auth
 */
final class UserProviderTest extends TestCase
{
    use ModelHelper;
    use UsersHelper;

    //region Tests

    /**
     * @param bool $withUser
     *
     * @return array
     */
    private function setUpRetrieveByIdTest(bool $withUser = true): array
    {
        $id = $this->getFaker()->numberBetween();
        $user = $this->createUserModel();
        $userManager = $this->createUserManager();
        $this->mockUserManagerGetUserById($userManager, $withUser ? $user : new ModelNotFoundException(), $id);
        $userProvider = $this->getUserProvider($userManager);

        return [$userProvider, $id, $user];
    }

    /**
     * @return void
     */
    public function testRetrieveById(): void
    {
        /** @var UserProvider $userProvider */
        [$userProvider, $id, $user] = $this->setUpRetrieveByIdTest();

        $this->assertEquals($user, $userProvider->retrieveById($id));
    }

    /**
     * @return void
     */
    public function testRetrieveByIdWithoutUser(): void
    {
        /** @var UserProvider $userProvider */
        [$userProvider, $id] = $this->setUpRetrieveByIdTest(false);

        $this->assertNull($userProvider->retrieveById($id));
    }

    /**
     * @param bool $withUser
     *
     * @return array
     */
    private function setUpRetrieveByCredentialsTest(bool $withUser = true): array
    {
        $email = $this->getFaker()->safeEmail;
        $user = $this->createUserModel();
        $userManager = $this->createUserManager();
        $this->mockUserManagerGetUserByEmail($userManager, $withUser ? $user : new ModelNotFoundException(), $email);
        $userProvider = $this->getUserProvider($userManager);

        return [$userProvider, $email, $user];
    }

    /**
     * @return void
     */
    public function testRetrieveByCredentials(): void
    {
        /** @var UserProvider $userProvider */
        [$userProvider, $email, $user] = $this->setUpRetrieveByCredentialsTest();

        $this->assertEquals($user, $userProvider->retrieveByCredentials(['email' => $email]));
    }

    /**
     * @return void
     */
    public function testRetrieveByCredentialsWithoutUser(): void
    {
        /** @var UserProvider $userProvider */
        [$userProvider, $email] = $this->setUpRetrieveByCredentialsTest(false);

        $this->assertNull($userProvider->retrieveByCredentials(['email' => $email]));
    }

    /**
     * @param bool $withValidCredentials
     *
     * @return array
     */
    private function setUpValidateCredentialsTest(bool $withValidCredentials = true): array
    {
        $email = $this->getFaker()->safeEmail;
        $password = $this->getFaker()->password;
        $userPassword = $this->getFaker()->sha256;
        $user = $this->createUserModel();
        $this->mockUserModelGetAuthPassword($user, $userPassword);
        $passwordHasher = $this->createPasswordHasher();
        $this->mockPasswordHasherCheck($passwordHasher, $withValidCredentials, $password, $userPassword);
        $userProvider = $this->getUserProvider(null, $passwordHasher);

        return [$userProvider, $user, $email, $password];
    }

    /**
     * @return void
     */
    public function testValidateCredentials(): void
    {
        /** @var UserProvider $userProvider */
        [$userProvider, $user, $email, $password] = $this->setUpValidateCredentialsTest();

        $this->assertTrue($userProvider->validateCredentials($user, ['email' => $email, 'password' => $password]));
    }

    /**
     * @return void
     */
    public function testValidateCredentialsWithInvalidCredentials(): void
    {
        /** @var UserProvider $userProvider */
        [$userProvider, $user, $email, $password] = $this->setUpValidateCredentialsTest(false);

        $this->assertFalse($userProvider->validateCredentials($user, ['email' => $email, 'password' => $password]));
    }

    /**
     * @return void
     */
    public function testValidateCredentialsWithoutPassword(): void
    {
        /** @var UserProvider $userProvider */
        [$userProvider, $user, $email] = $this->setUpValidateCredentialsTest();

        $this->assertFalse($userProvider->validateCredentials($user, ['email' => $email]));
    }

    //endregion

    /**
     * @param UserManager|null    $userManager
     * @param PasswordHasher|null $passwordHasher
     *
     * @return UserProvider
     */
    private function getUserProvider(UserManager $userManager = null, PasswordHasher $passwordHasher = null): UserProvider
    {
        return new UserProvider(
            $userManager ?: $this->createUserManager(),
            $passwordHasher ?: $this->createPasswordHasher()
        );
    }
}
