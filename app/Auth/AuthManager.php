<?php

namespace App\Auth;

use App\Users\UserModel;
use Illuminate\Auth\Access\AuthorizationException;
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
        $this->getGuard()->login($user);

        return $this;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return UserModel|Authenticatable
     */
    public function authenticate(string $email, string $password): UserModel
    {
        if (!$this->getGuard()->attempt([UserModel::PROPERTY_EMAIL => $email, UserModel::PROPERTY_PASSWORD => $password])) {
            throw new AuthorizationException();
        }

        return $this->getGuard()->user();
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
