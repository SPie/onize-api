<?php

namespace App\Models;

interface PasswordHasher
{
    public function hash(string $password): string;

    public function check(string $password, string $hashedPassword): bool;
}
