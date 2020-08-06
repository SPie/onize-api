<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\UsersController;
use App\Users\UserManager;
use Illuminate\Contracts\Routing\ResponseFactory;
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
        $userData = [$this->getFaker()->word => $this->getFaker()->word];
        $user = $this->createUserModel();
        $this->mockUserModelToArray($user, $userData);
        $userManager = $this->createUserManager();
        $this->mockUserManagerCreateUser($userManager, $user, $request->getEmail(), $request->getPassword());
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $response, ['user' => $userData], 201);
        $authManager = $this->createAuthManager();
        $usersController = $this->getUsersController($userManager, $responseFactory);

        return [$usersController, $request, $authManager, $response, $user];
    }

    /**
     * @return void
     */
    public function testRegister(): void
    {
        /** @var UsersController $usersController */
        [$usersController, $request, $authManager, $response, $user] = $this->setUpRegisterTest();

        $this->assertEquals($response, $usersController->register($request, $authManager));
        $this->assertAuthManagerLogin($authManager, $user);
    }

    //endregion

    /**
     * @param UserManager|null     $userManager
     * @param ResponseFactory|null $responseFactory
     *
     * @return UsersController
     */
    private function getUsersController(UserManager $userManager = null, ResponseFactory $responseFactory = null): UsersController
    {
        return new UsersController(
            $userManager ?: $this->createUserManager(),
            $responseFactory ?: $this->createResponseFactory()
        );
    }
}
