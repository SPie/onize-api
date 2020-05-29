<?php

namespace App\Users;

use App\Models\Model;
use App\Models\Timestampable;
use App\Models\UuidModel;
use SPie\LaravelJWT\Contracts\JWTAuthenticatable;

/**
 * Interface UserModel
 *
 * @package App\Users
 */
interface UserModel extends Model, JWTAuthenticatable, Timestampable, UuidModel
{
    const PROPERTY_EMAIL    = 'email';
    const PROPERTY_PASSWORD = 'password';
}
