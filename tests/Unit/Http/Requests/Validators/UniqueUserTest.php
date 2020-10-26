<?php

namespace Tests\Unit\Http\Requests\Validators;

use App\Http\Requests\Validators\UniqueUser;
use App\Models\Exceptions\ModelNotFoundException;
use App\Users\UserManager;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class UniqueUserTest
 *
 * @package Tests\Unit\Http\Requests\Validators
 */
final class UniqueUserTest extends TestCase
{
    use ModelHelper;
    use UsersHelper;

    //region Tests

    /**
     * @return void
     */
    public function testMessage(): void
    {
        $this->assertEquals('validation.user_not_unique', $this->getUniqueUser()->message());
    }

    /**
     * @param bool $emailUsed
     * @param bool $emailUsedAllowed
     *
     * @return array
     */
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

    /**
     * @return void
     */
    public function testPassesWithoutExistingEmail(): void
    {
        /** @var UniqueUser $rule */
        [$rule, $email] = $this->setUpPassesTest();

        $this->assertTrue($rule->passes($this->getFaker()->word, $email));
    }

    /**
     * @return void
     */
    public function testPassesWithExistingEmail(): void
    {
        /** @var UniqueUser $rule */
        [$rule, $email] = $this->setUpPassesTest(true);

        $this->assertFalse($rule->passes($this->getFaker()->word, $email));
    }

    /**
     * @return void
     */
    public function testPassesWithExistingEmailAllowed(): void
    {
        /** @var UniqueUser $rule */
        [$rule, $email] = $this->setUpPassesTest(true, true);

        $this->assertTrue($rule->passes($this->getFaker()->word, $email));
    }

    /**
     * @return void
     */
    public function testPassesWithEmptyEmail(): void
    {
        /** @var UniqueUser $rule */
        [$rule] = $this->setUpPassesTest();

        $this->assertTrue($rule->passes($this->getFaker()->word, null));
    }

    //endregion

    /**
     * @param UserManager|null $userManager
     *
     * @return UniqueUser
     */
    private function getUniqueUser(UserManager $userManager = null): UniqueUser
    {
        return new UniqueUser($userManager ?: $this->createUserManager());
    }
}
