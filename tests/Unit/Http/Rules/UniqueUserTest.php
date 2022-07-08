<?php

namespace Tests\Unit\Http\Rules;

use App\Http\Rules\UniqueUser;
use App\Models\Exceptions\ModelNotFoundException;
use App\Users\UserManager;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class UniqueUserTest extends TestCase
{
    use ModelHelper;
    use UsersHelper;

    private function getUniqueUser(UserManager $userManager = null): UniqueUser
    {
        return new UniqueUser($userManager ?: $this->createUserManager());
    }

    public function testMessage(): void
    {
        $this->assertEquals('validation.user-not-unique', $this->getUniqueUser()->message());
    }

    private function setUpPassesTest(bool $emailUsed = false, bool $emailUsedAllowed = false): array
    {
        $email = $this->getFaker()->safeEmail;
        $userId = $this->getFaker()->numberBetween();
        $user = $this->createUserModel();
        $this->mockModelGetId($user, $userId);
        $userManager = $this->createUserManager();
        $this->mockUserManagerGetUserByEmail($userManager, $emailUsed ? $user : new ModelNotFoundException(), $email);
        $rule = $this->getUniqueUser($userManager);
        if ($emailUsedAllowed) {
            $rule->setExistingUserId($userId);
        }

        return [$rule, $email];
    }

    public function testPassesWithoutExistingEmail(): void
    {
        /** @var UniqueUser $rule */
        [$rule, $email] = $this->setUpPassesTest();

        $this->assertTrue($rule->passes($this->getFaker()->word, $email));
    }

    public function testPassesWithExistingEmail(): void
    {
        /** @var UniqueUser $rule */
        [$rule, $email] = $this->setUpPassesTest(emailUsed: true);

        $this->assertFalse($rule->passes($this->getFaker()->word, $email));
    }

    public function testPassesWithExistingEmailAllowed(): void
    {
        /** @var UniqueUser $rule */
        [$rule, $email] = $this->setUpPassesTest(true, true);

        $this->assertTrue($rule->passes($this->getFaker()->word, $email));
    }

    public function testPassesWithEmptyEmail(): void
    {
        /** @var UniqueUser $rule */
        [$rule] = $this->setUpPassesTest();

        $this->assertTrue($rule->passes($this->getFaker()->word, null));
    }
}
