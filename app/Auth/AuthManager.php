<?php

namespace App\Auth;

use App\Users\UserModel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;

class AuthManager
{
    public function __construct(private StatefulGuard $guard, private UserProviderContract $userProvider)
    {
    }

    public function login(UserModel $user): self
    {
        $this->guard->login($user);

        return $this;
    }

    /**
     * @return UserModel|Authenticatable
     */
    public function authenticate(string $email, string $password): UserModel
    {
        if (!$this->guard->attempt(
            [UserModel::PROPERTY_EMAIL => $email, UserModel::PROPERTY_PASSWORD => $password],
            true
        )) {
            throw new AuthorizationException();
        }

        return $this->guard->user();
    }

    /**
     * @return UserModel|Authenticatable
     */
    public function authenticatedUser(): UserModel
    {
        $user = $this->guard->user();
        if (!$user) {
            throw new AuthenticationException();
        }

        return $user;
    }

    public function validateCredentials(UserModel $user, string $password): bool
    {
        return $this->userProvider->validateCredentials($user, [UserModel::PROPERTY_PASSWORD => $password]);
    }

    public function logout(): self
    {
        $this->guard->logout();

        return $this;
    }
}
