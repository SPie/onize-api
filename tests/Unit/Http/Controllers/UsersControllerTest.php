<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\UsersController;
use App\Http\Requests\Users\Register;
use App\Http\Requests\Users\Update;
use App\Users\UserManager;
use Illuminate\Contracts\Routing\ResponseFactory;
use Mockery as m;
use Mockery\MockInterface;
use Tests\Helper\AuthHelper;
use Tests\Helper\HttpHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class UsersControllerTest extends TestCase
{
    use AuthHelper;
    use HttpHelper;
    use UsersHelper;

    private function getUsersController(UserManager $userManager = null, ResponseFactory $responseFactory = null): UsersController
    {
        return new UsersController(
            $userManager ?: $this->createUserManager(),
            $responseFactory ?: $this->createResponseFactory()
        );
    }

    /**
     * @return Register|MockInterface
     */
    private function createRegister(string $email = null, string $password = null): Register
    {
        return m::spy(Register::class)
            ->shouldReceive('getEmail')
            ->andReturn($email ?: $this->getFaker()->safeEmail)
            ->getMock()
            ->shouldReceive('getPassword')
            ->andReturn($password ?: $this->getFaker()->password)
            ->getMock();
    }

    /**
     * @return Update|MockInterface
     */
    private function createUpdate(string $email = null): Update
    {
        return m::spy(Update::class)
            ->shouldReceive('getEmail')
            ->andReturn($email)
            ->getMock();
    }

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

    public function testRegister(): void
    {
        /** @var UsersController $usersController */
        [$usersController, $request, $authManager, $response, $user] = $this->setUpRegisterTest();

        $this->assertEquals($response, $usersController->register($request, $authManager));
        $this->assertAuthManagerLogin($authManager, $user);
    }

    private function setUpUpdateTest(bool $withChange = true): array
    {
        $email = $this->getFaker()->safeEmail;
        $request = $this->createUpdate($withChange ? $email : null);
        $user = $this->createUserModel();
        $authManager = $this->createAuthManager();
        $this->mockAuthManagerAuthenticatedUser($authManager, $user);
        $userData = [$this->getFaker()->word => $this->getFaker()->word];
        $updatedUser = $this->createUserModel();
        $this->mockUserModelToArray($updatedUser, $userData);
        $userManager = $this->createUserManager();
        $this->mockUserManagerUpdateUserData($userManager, $updatedUser, $user, $withChange ? $email : null);
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $response, ['user' => $userData]);
        $usersController = $this->getUsersController($userManager, $responseFactory);

        return [$usersController, $request, $authManager, $response];
    }

    public function testUpdateWithEmailChange(): void
    {
        /** @var UsersController $usersController */
        [$usersController, $request, $authManager, $response] = $this->setUpUpdateTest();

        $this->assertEquals($response, $usersController->update($request, $authManager));
    }

    public function testUpdateWithoutChange(): void
    {
        /** @var UsersController $usersController */
        [$usersController, $request, $authManager, $response] = $this->setUpUpdateTest(false);

        $this->assertEquals($response, $usersController->update($request, $authManager));
    }

    private function setUpUpdatePasswordTest(): array
    {
        $password = $this->getFaker()->password;
        $request = $this->createUpdatePasswordRequest($password);
        $user = $this->createUserModel();
        $authManager = $this->createAuthManager();
        $this->mockAuthManagerAuthenticatedUser($authManager, $user);
        $userData = [$this->getFaker()->word => $this->getFaker()->word];
        $updatedUser = $this->createUserModel();
        $this->mockUserModelToArray($updatedUser, $userData);
        $userManager = $this->createUserManager();
        $this->mockUserManagerUpdatePassword($userManager, $updatedUser, $user, $password);
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $response, ['user' => $userData]);
        $usersController = $this->getUsersController($userManager, $responseFactory);

        return [$usersController, $request, $authManager, $response];
    }

    public function testUpdatePassword(): void
    {
        /** @var UsersController $usersController */
        [$usersController, $request, $authManager, $response] = $this->setUpUpdatePasswordTest();

        $this->assertEquals($response, $usersController->updatePassword($request, $authManager));
    }
}
