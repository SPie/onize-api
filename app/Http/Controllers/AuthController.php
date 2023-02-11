<?php

namespace App\Http\Controllers;

use App\Auth\AuthManager;
use App\Http\Requests\Auth\Authenticate;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

final class AuthController extends Controller
{
    public const ROUTE_NAME_AUTHENTICATE  = 'auth.authenticate';
    public const ROUTE_NAME_AUTHENTICATED = 'auth.authenticated';
    public const ROUTE_NAME_LOGOUT        = 'auth.logout';
    public const ROUTE_NAME_REFRESH       = 'auth.refresh';

    public const RESPONSE_PARAMETER_USER = 'user';

    public function __construct(private AuthManager $authManager, ResponseFactory $responseFactory)
    {
        parent::__construct($responseFactory);
    }

    public function authenticate(Authenticate $request): JsonResponse
    {
        $user = $this->authManager->authenticate($request->getEmail(), $request->getPassword());

        return $this->getResponseFactory()->json([self::RESPONSE_PARAMETER_USER => $user->toArray()]);
    }

    public function authenticated(): JsonResponse
    {
        return $this->getResponseFactory()->json([
            self::RESPONSE_PARAMETER_USER => $this->authManager->authenticatedUser()->toArray()
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->authManager->logout();

        return $this->getResponseFactory()->json([], JsonResponse::HTTP_NO_CONTENT);
    }
}
