<?php

namespace App\Projects\Invites;

use App\Models\UuidGenerator;
use App\Projects\RoleModel;
use Carbon\CarbonImmutable;

/**
 * Class InvitationDoctrineModelFactory
 *
 * @package App\Projects\Invites
 */
final class InvitationDoctrineModelFactory implements InvitationModelFactory
{
    /**
     * @var UuidGenerator
     */
    private UuidGenerator $uuidGenerator;

    /**
     * @var int
     */
    private int $validUntilMinutes;

    /**
     * InvitationDoctrineModelFactory constructor.
     *
     * @param int $validUntilMinutes
     */
    public function __construct(UuidGenerator $uuidGenerator, int $validUntilMinutes)
    {
        $this->uuidGenerator = $uuidGenerator;
        $this->validUntilMinutes = $validUntilMinutes;
    }

    /**
     * @param RoleModel $role
     * @param string    $email
     * @param array     $metaData
     *
     * @return InvitationModel
     */
    public function create(RoleModel $role, string $email, array $metaData = []): InvitationModel
    {
        return new InvitationDoctrineModel(
            $this->uuidGenerator->generate(),
            $role,
            $email,
            (new CarbonImmutable())->addMinutes($this->validUntilMinutes),
            $metaData
        );
    }
}
