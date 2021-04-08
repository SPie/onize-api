<?php

namespace App\Projects\Invites;

use App\Models\Model;
use App\Models\Repository;

/**
 * Interface InvitationRepository
 *
 * @package App\Projects\Invites
 */
interface InvitationRepository extends Repository
{
    /**
     * @param InvitationModel|Model $model
     * @param bool                  $flush
     *
     * @return InvitationModel
     */
    public function save(Model $model, bool $flush = true): Model;
}
