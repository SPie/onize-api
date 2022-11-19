<?php

namespace App\Projects\Invites;

use App\Models\AbstractDoctrineRepository;
use App\Models\Model;

final class InvitationDoctrineRepository extends AbstractDoctrineRepository implements InvitationRepository
{
    public function findOneByUuid(string $uuid): InvitationModel|Model|null
    {
        return $this->findOneBy([InvitationModel::PROPERTY_UUID => $uuid]);
    }
}
