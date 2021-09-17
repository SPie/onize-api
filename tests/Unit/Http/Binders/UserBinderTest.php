<?php

namespace Tests\Unit\Http\Binders;

use App\Http\Binders\UserBinder;
use App\Models\Exceptions\ModelNotFoundException;
use App\Users\UserManager;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class UserBinderTest extends TestCase
{
    use UsersHelper;

    private function getUserBinder(UserManager $userManager = null): UserBinder
    {
        return new UserBinder($userManager ?: $this->createUserManager());
    }

    private function setUpBindTest(bool $withUser = true): array
    {
        $identifier = $this->getFaker()->uuid;
        $user = $this->createUserModel();
        $userManager = $this->createUserManager();
        $this->mockUserManagerGetUserByUuid($userManager, $withUser ? $user : new ModelNotFoundException(), $identifier);
        $userBinder = $this->getUserBinder($userManager);

        return [$userBinder, $identifier, $user];
    }

    public function testBind(): void
    {
        /** @var UserBinder $userBinder */
        [$userBinder, $identifier, $user] = $this->setUpBindTest();

        $this->assertEquals($user, $userBinder->bind($identifier));
    }

    public function testBindWithoutUser(): void
    {
        /** @var UserBinder $userBinder */
        [$userBinder, $identifier] = $this->setUpBindTest(withUser: false);

        $this->expectException(ModelNotFoundException::class);

        $userBinder->bind($identifier);
    }
}
