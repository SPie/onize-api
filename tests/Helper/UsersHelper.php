<?php

namespace Tests\Helper;

use App\Users\UserManager;
use App\Users\UserModel;
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
}
