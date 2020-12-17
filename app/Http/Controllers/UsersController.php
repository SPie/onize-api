<?php

namespace App\Http\Controllers;

use App\Auth\AuthManager;
use App\Http\Requests\Users\Register;
use App\Http\Requests\Users\Update;
use App\Http\Requests\Users\UpdatePassword;
use App\Users\UserManager;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

/**
 * Class UsersController
 *
 * @package App\Http\Controllers
 */
final class UsersController extends Controller
{
    public const ROUTE_NAME_REGISTER        = 'user.register';
    public const ROUTE_NAME_UPDATE          = 'user.update';
    public const ROUTE_NAME_UPDATE_PASSWORD = 'user.updatePassword';

    public const RESPONSE_PARAMETER_USER = 'user';

    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * UsersController constructor.
     *
     * @param UserManager     $userManager
     * @param ResponseFactory $responseFactory
     */
    public function __construct(UserManager $userManager, ResponseFactory $responseFactory)
    {
        parent::__construct($responseFactory);

        $this->userManager = $userManager;
    }

    /**
     * @return UserManager
     */
    private function getUserManager(): UserManager
    {
        return $this->userManager;
    }

    //region Controller actions

    /**
     * @param Register    $request
     * @param AuthManager $authManager
     *
     * @return JsonResponse
     */
    public function register(Register $request, AuthManager $authManager): JsonResponse
    {
        $user = $this->getUserManager()->createUser($request->getEmail(), $request->getPassword());

        $authManager->login($user);

        return $this->getResponseFactory()->json(
            [self::RESPONSE_PARAMETER_USER => $user->toArray()],
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * @param Update      $request
     * @param AuthManager $authManager
     *
     * @return JsonResponse
     */
    public function update(Update $request, AuthManager $authManager): JsonResponse
    {
        $user = $this->getUserManager()->updateUserData($authManager->authenticatedUser(), $request->getEmail());

        return $this->getResponseFactory()->json([
            self::RESPONSE_PARAMETER_USER => $user->toArray(),
        ]);
    }

    /**
     * @param UpdatePassword $request
     * @param AuthManager    $authManager
     *
     * @return JsonResponse
     */
    public function updatePassword(UpdatePassword $request, AuthManager $authManager): JsonResponse
    {
        $user = $this->getUserManager()->updatePassword($authManager->authenticatedUser(), $request->getUserPassword());

        return $this->getResponseFactory()->json([
            self::RESPONSE_PARAMETER_USER => $user->toArray()
        ]);
    }

    //endregion
}
