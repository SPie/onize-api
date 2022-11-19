<?php

namespace App\Http\Controllers;

use App\Auth\AuthManager;
use App\Http\Requests\Users\Register;
use App\Http\Requests\Users\Update;
use App\Http\Requests\Users\UpdatePassword;
use App\Users\UserManager;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

final class UsersController extends Controller
{
    public const ROUTE_NAME_REGISTER        = 'user.register';
    public const ROUTE_NAME_UPDATE          = 'user.update';
    public const ROUTE_NAME_UPDATE_PASSWORD = 'user.updatePassword';

    public const RESPONSE_PARAMETER_USER = 'user';

    public function __construct(private UserManager $userManager, ResponseFactory $responseFactory)
    {
        parent::__construct($responseFactory);
    }

    public function register(Register $request, AuthManager $authManager): JsonResponse
    {
        $user = $this->userManager->createUser($request->getEmail(), $request->getPassword());

        $authManager->login($user);

        return $this->getResponseFactory()->json(
            [self::RESPONSE_PARAMETER_USER => $user->toArray()],
            JsonResponse::HTTP_CREATED
        );
    }

    public function update(Update $request, AuthManager $authManager): JsonResponse
    {
        $user = $this->userManager->updateUserData($authManager->authenticatedUser(), $request->getEmail());

        return $this->getResponseFactory()->json([
            self::RESPONSE_PARAMETER_USER => $user->toArray(),
        ]);
    }

    public function updatePassword(UpdatePassword $request, AuthManager $authManager): JsonResponse
    {
        $user = $this->userManager->updatePassword($authManager->authenticatedUser(), $request->getUserPassword());

        return $this->getResponseFactory()->json([
            self::RESPONSE_PARAMETER_USER => $user->toArray()
        ]);
    }
}
