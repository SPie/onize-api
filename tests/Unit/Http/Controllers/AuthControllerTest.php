<?php

namespace Tests\Unit\Http\Controllers;

use App\Auth\AuthManager;
use App\Http\Controllers\AuthController;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Tests\Helper\AuthHelper;
use Tests\Helper\HttpHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class AuthControllerTest extends TestCase
{
    use AuthHelper;
    use HttpHelper;
    use UsersHelper;

    private function getAuthController(AuthManager $authManager = null, ResponseFactory $responseFactory = null): AuthController
    {
        return new AuthController(
            $authManager ?: $this->createAuthManager(),
            $responseFactory ?: $this->createResponseFactory()
        );
    }

    private function setUpAuthenticatedTest(): array
    {
        $userData = [$this->getFaker()->word => $this->getFaker()->word];
        $user = $this->createUserModel();
        $this->mockUserModelToArray($user, $userData);
        $authManager = $this->createAuthManager();
        $this->mockAuthManagerAuthenticatedUser($authManager, $user);
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $response, ['user' => $userData]);
        $authController = $this->getAuthController($authManager, $responseFactory);

        return [$authController, $response];
    }

    public function testAuthenticated(): void
    {
        /** @var AuthController $authController */
        [$authController, $response] = $this->setUpAuthenticatedTest();

        $this->assertEquals($response, $authController->authenticated());
    }

    private function setUpAuthenticateTest(bool $withValidCredentials = true): array
    {
        $request = $this->createAuthenticateRequest();
        $userData = [$this->getFaker()->word => $this->getFaker()->word];
        $user = $this->createUserModel();
        $this->mockUserModelToArray($user, $userData);
        $authManager = $this->createAuthManager();
        $this->mockAuthManagerAuthenticate(
            $authManager,
            $withValidCredentials ? $user : new AuthorizationException(),
            $request->getEmail(),
            $request->getPassword()
        );
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $response, ['user' => $userData]);
        $authController = $this->getAuthController($authManager, $responseFactory);

        return [$authController, $request, $response];
    }

    public function testAuthenticate(): void
    {
        /** @var AuthController $authController */
        [$authController, $request, $response] = $this->setUpAuthenticateTest();

        $this->assertEquals($response, $authController->authenticate($request));
    }

    public function testAuthenticateWithInvalidCredentials(): void
    {
        /** @var AuthController $authController */
        [$authController, $request] = $this->setUpAuthenticateTest(false);

        $this->expectException(AuthorizationException::class);

        $authController->authenticate($request);
    }

    public function testLogout(): void
    {
        $authManager = $this->createAuthManager();
        $response = $this->createJsonResponse();
        $responseFactory = $this->createResponseFactory();
        $this->mockResponseFactoryJson($responseFactory, $response, [], 204);

        $this->assertEquals($response, $this->getAuthController($authManager, $responseFactory)->logout());
        $authManager->shouldHaveReceived('logout')->once();
    }
}
