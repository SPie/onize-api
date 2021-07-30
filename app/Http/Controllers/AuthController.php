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
    public const ROUTE_NAME_AUTHENTICATE  = 'auth.authenticate';
    public const ROUTE_NAME_AUTHENTICATED = 'auth.authenticated';
    public const ROUTE_NAME_LOGOUT        = 'auth.logout';

    public const RESPONSE_PARAMETER_USER = 'user';

    /**
     * AuthController constructor.
     *
     * @param AuthManager     $authManager
     * @param ResponseFactory $responseFactory
     */
    public function __construct(private AuthManager $authManager, ResponseFactory $responseFactory)
    {
        parent::__construct($responseFactory);
    }

    //region Controller actions

    /**
     * @param Authenticate $request
     *
     * @return JsonResponse
     */
    public function authenticate(Authenticate $request): JsonResponse
    {
        $this->authManager->authenticate($request->getEmail(), $request->getPassword());

        return $this->getResponseFactory()->json([], JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @return JsonResponse
     */
    public function authenticated(): JsonResponse
    {
        return $this->getResponseFactory()->json([
            self::RESPONSE_PARAMETER_USER => $this->authManager->authenticatedUser()->toArray()
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->authManager->logout();

        return $this->getResponseFactory()->json([], JsonResponse::HTTP_NO_CONTENT);
    }

    //endregion
}
