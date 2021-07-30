<?php

namespace App\Projects;

use App\Models\Model;
use App\Models\Repository;

/**
 * Interface MemberRepository
 *
 * @package App\Projects
 */
interface MemberRepository extends Repository
{
    /**
     * @param Model $model
     * @param bool  $flush
     *
     * @return MemberModel|Model
     */
    public function save(Model $model, bool $flush = true): Model;
}
