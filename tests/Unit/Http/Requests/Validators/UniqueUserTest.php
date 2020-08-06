<?php

namespace Tests\Unit\Http\Requests\Validators;

use App\Http\Requests\Validators\UniqueUser;
use App\Users\UserManager;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class UniqueUserTest
 *
 * @package Tests\Unit\Http\Requests\Validators
 */
final class UniqueUserTest extends TestCase
{
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
     *
     * @return array
     */
    private function setUpPassesTest(bool $emailUsed = false): array
    {
        $email = $this->getFaker()->safeEmail;
        $userManager = $this->createUserManager();
        $this->mockUserManagerIsEmailUsed($userManager, $emailUsed, $email);
        $rule = $this->getUniqueUser($userManager);

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
