<?php

namespace Tests\Unit\Http\Controllers;

use App\Auth\AuthManager;
use App\Http\Controllers\AuthController;
use Illuminate\Contracts\Routing\ResponseFactory;
use Tests\Helper\AuthHelper;
use Tests\Helper\HttpHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class AuthControllerTest
 *
 * @package Tests\Unit\Http\Controllers
 */
final class AuthControllerTest extends TestCase
{
    use AuthHelper;
    use HttpHelper;
    use UsersHelper;

    //region Tests

    /**
     * @return array
     */
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

    /**
     * @return void
     */
    public function testAuthenticated(): void
    {
        /** @var AuthController $authController */
        [$authController, $response] = $this->setUpAuthenticatedTest();

        $this->assertEquals($response, $authController->authenticated());
    }

    //endregion

    /**
     * @param AuthManager|null     $authManager
     * @param ResponseFactory|null $responseFactory
     *
     * @return AuthController
     */
    private function getAuthController(AuthManager $authManager = null, ResponseFactory $responseFactory = null): AuthController
    {
        return new AuthController(
            $authManager ?: $this->createAuthManager(),
            $responseFactory ?: $this->createResponseFactory()
        );
    }
}
