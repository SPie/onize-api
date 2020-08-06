<?php

namespace App\Auth;

use App\Users\UserModel;
use SPie\LaravelJWT\Contracts\JWTGuard;

/**
 * Class AuthManager
 *
 * @package App\Auth
 */
class AuthManager
{

    /**
     * @var JWTGuard
     */
    private JWTGuard $jwtGuard;

    /**
     * JWTManager constructor.
     *
     * @param JWTGuard $jwtGuard
     */
    public function __construct(JWTGuard $jwtGuard)
    {
        $this->jwtGuard = $jwtGuard;
    }

    /**
     * @return JWTGuard
     */
    private function getJwtGuard(): JWTGuard
    {
        return $this->jwtGuard;
    }

    /**
     * @param UserModel    $user
     *
     * @return $this
     */
    public function login(UserModel $user): self
    {
        $this->getJwtGuard()->login($user, true);

        return $this;
    }
}
