<?php

namespace App\Projects\Invites;

use App\Models\AbstractDoctrineRepository;
use App\Models\Model;

/**
 * Class InvitationDoctrineRepository
 *
 * @package App\Projects\Invites
 */
final class InvitationDoctrineRepository extends AbstractDoctrineRepository implements InvitationRepository
{
    /**
     * @param string $uuid
     *
     * @return InvitationModel|Model|null
     */
    public function findOneByUuid(string $uuid): ?InvitationModel
    {
        return $this->findOneBy([InvitationModel::PROPERTY_UUID => $uuid]);
    }
}
