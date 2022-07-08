<?php

namespace Tests\Unit\Users;

use App\Models\Exceptions\ModelNotFoundException;
use App\Users\UserManager;
use App\Users\UserModelFactory;
use App\Users\UserRepository;
use Mockery\MockInterface;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class UserManagerTest extends TestCase
{
    use ModelHelper;
    use UsersHelper;

    private function getUserManager(UserRepository $userRepository = null, UserModelFactory $userModelFactory = null): UserManager
    {
        return new UserManager(
            $userRepository ?: $this->createUserRepository(),
            $userModelFactory ?: $this->createUserModelFactory()
        );
    }

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

    public function testCreateUser(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $email, $password, $user, $userRepository] = $this->setUpCreateUserTest();

        $this->assertEquals($user, $userManager->createUser($email, $password));
        $this->assertRepositorySave($userRepository, $user);
    }

    private function setUpGetUserByIdTest(bool $withUser = true): array
    {
        $id = $this->getFaker()->numberBetween();
        $user = $this->createUserModel();
        $userRepository = $this->createUserRepository();
        $this->mockRepositoryFind($userRepository, $withUser ? $user : null, $id);
        $userManager = $this->getUserManager($userRepository);

        return [$userManager, $id, $user];
    }

    public function testGetUserById(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $id, $user] = $this->setUpGetUserByIdTest();

        $this->assertEquals($user, $userManager->getUserById($id));
    }

    public function testGetUserByIdWithoutUser(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $id] = $this->setUpGetUserByIdTest(false);

        $this->expectException(ModelNotFoundException::class);

        $userManager->getUserById($id);
    }

    private function setUpGetUserByEmailTest(bool $withUser = true): array
    {
        $email = $this->getFaker()->safeEmail;
        $user = $this->createUserModel();
        $userRepository = $this->createUserRepository();
        $this->mockUserRepositoryFindOneByEmail($userRepository, $withUser ? $user : null, $email);
        $userManager = $this->getUserManager($userRepository);

        return [$userManager, $email, $user];
    }

    public function testGetUserByEmail(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $email, $user] = $this->setUpGetUserByEmailTest();

        $this->assertEquals($user, $userManager->getUserByEmail($email));
    }

    public function testGetUserByEmailWithoutUser(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $email] = $this->setUpGetUserByEmailTest(false);

        $this->expectException(ModelNotFoundException::class);

        $userManager->getUserByEmail($email);
    }

    private function setUpUpdateUserDataTest(bool $withChange = true): array
    {
        $email = $this->getFaker()->safeEmail;
        $user = $this->createUserModel();
        $this->mockUserModelSetEmail($user, $email);
        $userRepository = $this->createUserRepository();
        if ($withChange) {
            $this->mockRepositorySave($userRepository, $user);
        }
        $userManager = $this->getUserManager($userRepository);

        return [$userManager, $user, $email, $userRepository];
    }

    public function testUpdateUserDataWithChange(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $user, $email, $userRepository] = $this->setUpUpdateUserDataTest();

        $this->assertEquals($user, $userManager->updateUserData($user, $email));
        $this->assertRepositorySave($userRepository, $user);
        $this->assertUserModelSetEmail($user, $email);
    }

    public function testUpdateUserDataWithoutChange(): void
    {
        /**
         * @var UserManager    $userManager
         * @var UserRepository|MockInterface $userRepository
         */
        [$userManager, $user, $email, $userRepository] = $this->setUpUpdateUserDataTest(false);

        $this->assertEquals($user, $userManager->updateUserData($user, null));
        $userRepository->shouldNotHaveReceived('save');
    }

    private function setUpUpdatePasswordTest(): array
    {
        $user = $this->createUserModel();
        $password = $this->getFaker()->password;
        $updatedUser = $this->createUserModel();
        $userModelFactory = $this->createUserModelFactory();
        $this->mockUserModelFactorySetPassword($userModelFactory, $updatedUser, $user, $password);
        $userRepository = $this->createUserRepository();
        $this->mockRepositorySave($userRepository, $updatedUser);
        $userManager = $this->getUserManager($userRepository, $userModelFactory);

        return [$userManager, $user, $password, $updatedUser, $userRepository];
    }

    public function testUpdatePassword(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $user, $password, $updatedUser, $userRepository] = $this->setUpUpdatePasswordTest();

        $this->assertEquals($updatedUser, $userManager->updatePassword($user, $password));
        $this->assertRepositorySave($userRepository, $updatedUser);
    }

    private function setUpGetUserByUuidTest(bool $withUser = true): array
    {
        $uuid = $this->getFaker()->uuid;
        $user = $this->createUserModel();
        $userRepository = $this->createUserRepository();
        $this->mockUserRepositoryFindOneByUuid($userRepository, $withUser ? $user : null, $uuid);
        $userManager = $this->getUserManager($userRepository);

        return [$userManager, $uuid, $user];
    }

    public function testGetUserByUuid(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $uuid, $user] = $this->setUpGetUserByUuidTest();

        $this->assertEquals($user, $userManager->getUserByUuid($uuid));
    }

    public function testGetUserByUuidWithoutUser(): void
    {
        /** @var UserManager $userManager */
        [$userManager, $uuid] = $this->setUpGetUserByUuidTest(withUser: false);

        $this->expectException(ModelNotFoundException::class);

        $userManager->getUserByUuid($uuid);
    }
}
