<?php

namespace Tests\Unit\Users;

use App\Models\Exceptions\ModelNotFoundException;
use App\Users\UserManager;
use App\Users\UserModelFactory;
use App\Users\UserRepository;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class UserManagerTest
 *
 * @package Tests\Unit\Users
 */
final class UserManagerTest extends TestCase
{
    use ModelHelper;
    use UsersHelper;

    //region Tests

    private function setUpCreateUserTest(): array
    {
        $email = $this->getFaker()->safeEmail;
        $password = $this->getFaker()->password;
        $user = $this->createUserModel();
        $userModelFactory = $this->createUserModelFactory();
        $this->mockUserModelFactoryCreate($userModelFactory, $user, $email, $password);
        $userRepository = $this->createUserRepository();
        $this->mockRepositorySave($userRepository, $user);
        $userManager = $this->getUserManager($userRepository, $userModelFactory);

        return [$userManager, $email, $password, $user, $userRepository];
    }

    /**
     * @return void
     */
    public function testCreateUser(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $email, $password, $user, $userRepository] = $this->setUpCreateUserTest();

        $this->assertEquals($user, $userManager->createUser($email, $password));
        $this->assertRepositorySave($userRepository, $user);
    }

    /**
     * @param bool $isEmailUsed
     *
     * @return array
     */
    private function setUpIsEmailUsedTest(bool $isEmailUsed = true): array
    {
        $email = $this->getFaker()->safeEmail;
        $user = $this->createUserModel();
        $userRepository = $this->createUserRepository();
        $this->mockUserRepositoryFindOneByEmail($userRepository, $isEmailUsed ? $user : null, $email);
        $userManager = $this->getUserManager($userRepository);

        return [$userManager, $email];
    }

    /**
     * @return void
     */
    public function testIsEmailUsed(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $email] = $this->setUpIsEmailUsedTest();

        $this->assertTrue($userManager->isEmailUsed($email));
    }

    /**
     * @return void
     */
    public function testIsEmailUsedWithoutUsedEmail(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $email] = $this->setUpIsEmailUsedTest(false);

        $this->assertFalse($userManager->isEmailUsed($email));
    }

    /**
     * @param bool $withUser
     *
     * @return array
     */
    private function setUpGetUserByIdTest(bool $withUser = true): array
    {
        $id = $this->getFaker()->numberBetween();
        $user = $this->createUserModel();
        $userRepository = $this->createUserRepository();
        $this->mockRepositoryFind($userRepository, $withUser ? $user : null, $id);
        $userManager = $this->getUserManager($userRepository);

        return [$userManager, $id, $user];
    }

    /**
     * @return void
     */
    public function testGetUserById(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $id, $user] = $this->setUpGetUserByIdTest();

        $this->assertEquals($user, $userManager->getUserById($id));
    }

    /**
     * @return void
     */
    public function testGetUserByIdWithoutUser(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $id] = $this->setUpGetUserByIdTest(false);

        $this->expectException(ModelNotFoundException::class);

        $userManager->getUserById($id);
    }

    /**
     * @param bool $withUser
     *
     * @return array
     */
    private function setUpGetUserByEmailTest(bool $withUser = true): array
    {
        $email = $this->getFaker()->safeEmail;
        $user = $this->createUserModel();
        $userRepository = $this->createUserRepository();
        $this->mockUserRepositoryFindOneByEmail($userRepository, $withUser ? $user : null, $email);
        $userManager = $this->getUserManager($userRepository);

        return [$userManager, $email, $user];
    }

    /**
     * @return void
     */
    public function testGetUserByEmail(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $email, $user] = $this->setUpGetUserByEmailTest();

        $this->assertEquals($user, $userManager->getUserByEmail($email));
    }

    /**
     * @return void
     */
    public function testGetUserByEmailWithoutUser(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $email] = $this->setUpGetUserByEmailTest(false);

        $this->expectException(ModelNotFoundException::class);

        $userManager->getUserByEmail($email);
    }

    //endregion

    /**
     * @param UserRepository|null   $userRepository
     * @param UserModelFactory|null $userModelFactory
     *
     * @return UserManager
     */
    private function getUserManager(UserRepository $userRepository = null, UserModelFactory $userModelFactory = null): UserManager
    {
        return new UserManager(
            $userRepository ?: $this->createUserRepository(),
            $userModelFactory ?: $this->createUserModelFactory()
        );
    }
}
