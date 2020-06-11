<?php

namespace App\Auth;

use App\Users\UserModel;
use Illuminate\Http\JsonResponse;
use SPie\LaravelJWT\Contracts\JWTGuard;
use SPie\LaravelJWT\Contracts\JWTHandler;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JWTManager
 *
 * @package App\Auth
 */
class JWTManager
{

    /**
     * @var JWTGuard
     */
    private JWTGuard $jwtGuard;

    /**
     * @var JWTHandler
     */
    private JWTHandler $jwtHandler;

    /**
     * JWTManager constructor.
     *
     * @param JWTGuard   $jwtGuard
     * @param JWTHandler $jwtHandler
     */
    public function __construct(JWTGuard $jwtGuard, JWTHandler $jwtHandler)
    {
        $this->jwtGuard = $jwtGuard;
        $this->jwtHandler = $jwtHandler;
    }

    /**
     * @return JWTGuard
     */
    private function getJwtGuard(): JWTGuard
    {
        return $this->jwtGuard;
    }

    /**
     * @return JWTHandler
     */
    private function getJwtHandler(): JWTHandler
    {
        return $this->jwtHandler;
    }

    /**
     * @param UserModel $user
     * @param JsonResponse  $response
     * @param bool      $withRefreshToken
     *
     * @return JsonResponse|Response
     */
    public function issueTokens(UserModel $user, JsonResponse $response, bool $withRefreshToken = false): JsonResponse
    {
        $this->getJwtGuard()->setUser($user);

        $this->getJwtGuard()->issueAccessToken($user);

        if ($withRefreshToken) {
            $this->getJwtGuard()->issueRefreshToken();

            $response = $this->getJwtGuard()->returnRefreshToken($response);
        }

        return $this->getJwtGuard()->returnAccessToken($response);
    }
}
