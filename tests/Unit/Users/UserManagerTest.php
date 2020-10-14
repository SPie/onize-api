<?php

namespace Tests\Unit\Users;

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
     * @return array
     */
    private function setUpRetrieveByIdTest(bool $withUser = true): array
    {
        $identifier = $this->getFaker()->numberBetween();
        $user = $this->createUserModel();
        $userRepository = $this->createUserRepository();
        $this->mockRepositoryFind($userRepository, $withUser ? $user : null, $identifier);
        $userManager = $this->getUserManager($userRepository);

        return [$userManager, $identifier, $user];
    }

    /**
     * @return void
     */
    public function testRetrieveById(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $identifier, $user] = $this->setUpRetrieveByIdTest();

        $this->assertEquals($user, $userManager->retrieveById($identifier));
    }

    /**
     * @return void
     */
    public function testRetrieveByIdWithoutUser(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $identifier, $user] = $this->setUpRetrieveByIdTest(false);

        $this->assertEmpty($userManager->retrieveById($identifier));
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
