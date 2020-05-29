<?php

namespace App\Http\Controllers;

use App\Auth\JWTManager;
use App\Http\Requests\Users\Register;
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
    const ROUTE_NAME_REGISTER = 'user.register';

    const RESPONSE_PARAMETER_USER = 'user';

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
     * @param Register   $request
     * @param JWTManager $jwtManager
     *
     * @return JsonResponse
     */
    public function register(Register $request, JWTManager $jwtManager): JsonResponse
    {
        $user = $this->getUserManager()->createUser($request->getEmail(), $request->getPassword());

        return $jwtManager->issueTokens(
            $user,
            $this->getResponseFactory()->json(
                [self::RESPONSE_PARAMETER_USER => $user->toArray()],
                JsonResponse::HTTP_CREATED
            ),
            $request->shouldRemember()
        );
    }

    //endregion
}
