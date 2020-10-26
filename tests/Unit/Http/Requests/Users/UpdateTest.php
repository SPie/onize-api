<?php

namespace Tests\Unit\Http\Requests\Users;

use App\Auth\AuthManager;
use App\Http\Requests\Users\Update;
use App\Http\Requests\Validators\UniqueUser;
use Tests\Helper\AuthHelper;
use Tests\Helper\HttpHelper;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class UpdateTest
 *
 * @package Tests\Unit\Http\Requests\Users
 */
final class UpdateTest extends TestCase
{
    use AuthHelper;
    use HttpHelper;
    use ModelHelper;
    use UsersHelper;

    //region

    /**
     * @return void
     */
    public function testRules(): void
    {
        $user = $this->createUserModel();
        $this->mockModelGetId($user, $this->getFaker()->numberBetween());
        $authManager = $this->createAuthManager();
        $this->mockAuthManagerAuthenticatedUser($authManager, $user);
        $uniqueUser = $this->createUniqueUser();
        $this->mockUniqueUserSetExistingUserId($uniqueUser, $user->getId());

        $this->assertEquals(
            [
                'email' => [
                    'email',
                    $uniqueUser,
                ]
            ],
            $this->getUpdate($uniqueUser, $authManager)->rules()
        );
        $this->assertUniqueUserSetExistingUserId($uniqueUser, $user->getId());
    }

    /**
     * @return void
     */
    public function testGetEmail(): void
    {
        $email = $this->getFaker()->safeEmail;
        $request = $this->getUpdate();
        $request->offsetSet('email', $email);

        $this->assertEquals($email, $request->getEmail());
    }

    /**
     * @return void
     */
    public function testGetEmailWithoutEmail(): void
    {
        $this->assertNull($this->getUpdate()->getEmail());
    }

    //endregion

    /**
     * @param UniqueUser|null  $uniqueUser
     * @param AuthManager|null $authManager
     *
     * @return Update
     */
    private function getUpdate(UniqueUser $uniqueUser = null, AuthManager $authManager = null): Update
    {
        return new Update(
            $uniqueUser ?: $this->createUniqueUser(),
            $authManager ?: $this->createAuthManager()
        );
    }
}
