<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Hashing\HashManager;

final class LaravelPasswordHasher implements PasswordHasher
{

    public function __construct(readonly private HashManager $hashManager)
    {
    }

    private function getHashManager(): HashManager
    {
        return $this->hashManager;
    }

    public function hash(string $password): string
    {
        return $this->getHashManager()->make($password);
    }

    public function check(string $password, string $hashedPassword): bool
    {
        return $this->getHashManager()->check($password, $hashedPassword);
    }
}
