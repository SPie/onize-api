<?php

namespace App\Http\Controllers;

use App\Auth\AuthManager;
use App\Http\Requests\Auth\Authenticate;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers
 */
final class AuthController extends Controller
{
    const ROUTE_NAME_AUTHENTICATE  = 'auth.authenticate';
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
     * @param Authenticate $request
     *
     * @return JsonResponse
     */
    public function authenticate(Authenticate $request): JsonResponse
    {
        $this->getAuthManager()->authenticate($request->getEmail(), $request->getPassword());

        return $this->getResponseFactory()->json([], JsonResponse::HTTP_NO_CONTENT);
    }

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
