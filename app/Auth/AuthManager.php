<?php

namespace App\Auth;

use App\Users\UserModel;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;

/**
 * Class AuthManager
 *
 * @package App\Auth
 */
class AuthManager
{

    /**
     * @var StatefulGuard
     */
    private StatefulGuard $guard;

    /**
     * JWTManager constructor.
     *
     * @param StatefulGuard $guard
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * @return StatefulGuard
     */
    private function getGuard(): StatefulGuard
    {
        return $this->guard;
    }

    /**
     * @param UserModel    $user
     *
     * @return $this
     */
    public function login(UserModel $user): self
    {
        $this->getGuard()->login($user, true);

        return $this;
    }

    /**
     * @return UserModel|Authenticatable
     */
    public function authenticatedUser(): UserModel
    {
        $user = $this->getGuard()->user();
        if (!$user) {
            throw new AuthenticationException();
        }

        return $user;
    }
}
