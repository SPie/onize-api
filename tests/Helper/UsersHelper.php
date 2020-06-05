<?php

namespace Tests\Helper;

use App\Users\UserManager;
use App\Users\UserModel;
use App\Users\UserModelFactory;
use App\Users\UserRepository;
use Mockery as m;
use Mockery\MockInterface;

/**
 * Trait UsersHelper
 *
 * @package Tests\Helper
 */
trait UsersHelper
{
    /**
     * @return UserModel|MockInterface
     */
    private function createUserModel(): UserModel
    {
        return m::spy(UserModel::class);
    }

    /**
     * @param UserModel|MockInterface $user
     * @param array                   $data
     *
     * @return $this
     */
    private function mockUserModelToArray(MockInterface $user, array $data): self
    {
        $user
            ->shouldReceive('toArray')
            ->andReturn($data);

        return $this;
    }

    /**
     * @return UserManager|MockInterface
     */
    private function createUserManager(): UserManager
    {
        return m::spy(UserManager::class);
    }

    /**
     * @param UserManager|MockInterface $userManager
     * @param UserModel|\Exception      $user
     * @param string                    $email
     * @param string                    $password
     *
     * @return $this
     */
    private function mockUserManagerCreateUser(MockInterface $userManager, $user, string $email, string $password): self
    {
        $userManager
            ->shouldReceive('createUser')
            ->with($email, $password)
            ->andThrow($user);

        return $this;
    }

    /**
     * @param UserManager|MockInterface $userManager
     * @param bool                      $emailUsed
     * @param string                    $email
     *
     * @return $this
     */
    private function mockUserManagerIsEmailUsed(MockInterface $userManager, bool $emailUsed, string $email): self
    {
        $userManager
            ->shouldReceive('isEmailUsed')
            ->with($email)
            ->andReturn($emailUsed);

        return $this;
    }

    /**
     * @return UserModelFactory|MockInterface
     */
    private function createUserModelFactory(): UserModelFactory
    {
        return m::spy(UserModelFactory::class);
    }

    /**
     * @param UserModelFactory|MockInterface $userModelFactory
     * @param UserModel                      $user
     * @param string                         $email
     * @param string                         $password
     *
     * @return $this
     */
    private function mockUserModelFactoryCreate(
        MockInterface $userModelFactory,
        UserModel $user,
        string $email,
        string $password
    ): self {
        $userModelFactory
            ->shouldReceive('create')
            ->with($email, $password)
            ->andReturn($user);

        return $this;
    }

    /**
     * @return UserRepository|MockInterface
     */
    private function createUserRepository(): UserRepository
    {
        return m::spy(UserRepository::class);
    }

    /**
     * @param UserRepository|MockInterface $userRepository
     * @param UserModel|null               $user
     * @param string                       $email
     *
     * @return $this
     */
    private function mockUserRepositoryFindOneByEmail(MockInterface $userRepository, ?UserModel $user, string $email): self
    {
        $userRepository
            ->shouldReceive('findOneByEmail')
            ->with($email)
            ->andReturn($user);

        return $this;
    }
}
