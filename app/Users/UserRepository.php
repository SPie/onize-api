<?php

namespace App\Users;

use App\Models\Model;
use App\Models\Repository;

/**
 * Interface UserRepository
 *
 * @package App\Users
 */
interface UserRepository extends Repository
{
    /**
     * @param UserModel|Model $model
     * @param bool            $flush
     *
     * @return UserModel|Model
     */
    public function save(Model $model, bool $flush = true): Model;
}
