<?php

namespace App\Models;

/**
 * Interface PasswordHasher
 *
 * @package App\Models
 */
interface PasswordHasher
{
    /**
     * @param string $password
     *
     * @return string
     */
    public function hash(string $password): string;
}
