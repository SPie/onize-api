<?php

namespace App\Projects\Invites;

use App\Models\Model;
use App\Models\Repository;

interface InvitationRepository extends Repository
{
    public function save(InvitationModel|Model $model, bool $flush = true): InvitationModel|Model;

    public function findOneByUuid(string $uuid): InvitationModel|Model|null;
}
