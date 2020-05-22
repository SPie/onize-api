<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\UsersController;
use App\Users\UserManager;
use Tests\Helper\AuthHelper;
use Tests\Helper\HttpHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class UsersControllerTest
 *
 * @package Tests\Unit\Http\Controllers
 */
final class UsersControllerTest extends TestCase
{
    use AuthHelper;
    use HttpHelper;
    use UsersHelper;

    //region Tests

    private function setUpRegisterTest(): array
    {
        $request = $this->createRegister();
        $jwtManager = $this->createJWTManager();
        $user = $this->createUserModel();
        $userManager = $this->createUserManager();
        $this->mockUserManagerCreateUser($userManager, $user, $request->getEmail(), $request->getPassword());
        $usersController = $this->getUsersController($userManager);

        return [$usersController, $request, $jwtManager];
    }

    //endregion

    /**
     * @param UserManager|null $userManager
     *
     * @return UsersController
     */
    private function getUsersController(UserManager $userManager = null): UsersController
    {
        return new UsersController($userManager ?: $this->createUserManager());
    }
}
