<?php

namespace App\Projects;

use App\Models\Model;
use App\Models\Repository;

interface MemberRepository extends Repository
{
    public function save(MemberModel|Model $model, bool $flush = true): MemberModel|Model;
}
