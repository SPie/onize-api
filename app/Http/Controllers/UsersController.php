<?php

namespace App\Http\Controllers;

use App\Auth\JWTManager;
use App\Http\Requests\Users\Register;
use App\Users\UserManager;
use Illuminate\Http\JsonResponse;

/**
 * Class UsersController
 *
 * @package App\Http\Controllers
 */
final class UsersController extends Controller
{
    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * UsersController constructor.
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
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
        // TODO
    }

    //endregion
}
