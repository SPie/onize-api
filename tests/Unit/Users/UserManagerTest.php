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
