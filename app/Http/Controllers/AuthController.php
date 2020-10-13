<?php

namespace App\Http\Controllers;

use App\Auth\AuthManager;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers
 */
final class AuthController extends Controller
{
    const ROUTE_NAME_AUTHENTICATED = 'auth.authenticated';

    const RESPONSE_PARAMETER_USER = 'user';

    /**
     * @var AuthManager
     */
    private AuthManager $authManager;

    /**
     * AuthController constructor.
     *
     * @param AuthManager     $authManager
     * @param ResponseFactory $responseFactory
     */
    public function __construct(AuthManager $authManager, ResponseFactory $responseFactory)
    {
        parent::__construct($responseFactory);

        $this->authManager = $authManager;
    }

    /**
     * @return AuthManager
     */
    private function getAuthManager(): AuthManager
    {
        return $this->authManager;
    }

    //region Controller actions

    /**
     * @return JsonResponse
     */
    public function authenticated(): JsonResponse
    {
        return $this->getResponseFactory()->json([
            self::RESPONSE_PARAMETER_USER => $this->getAuthManager()->authenticatedUser()->toArray()
        ]);
    }

    //endregion
}
