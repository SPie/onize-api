<?php

namespace App\Projects;

use App\Models\AbstractDoctrineModel;
use App\Models\SoftDelete;
use App\Models\Timestamps;
use App\Users\UserModel;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="members")
 * @ORM\Entity(repositoryClass="App\Projects\MemerDoctrineRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class MemberDoctrineModel extends AbstractDoctrineModel implements MemberModel
{
    use SoftDelete;
    use Timestamps;

    /**
     * @ORM\ManyToOne(targetEntity="App\Users\UserDoctrineModel", inversedBy="members", cascade={"persist"})
     */
    private UserModel $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Projects\RoleDoctrineModel", inversedBy="members", cascade={"persist"})
     */
    private RoleModel $role;

    /**
     * @ORM\Column(name="meta_data", type="string", length=255, nullable=false)
     */
    private string $metaData;

    public function __construct(UserModel $user, RoleModel $role, array $metaData = [])
    {
        $this->user = $user;
        $this->role = $role;
        $this->metaData = \json_encode($metaData);
    }

    public function getUser(): UserModel
    {
        return $this->user;
    }

    public function setRole(RoleModel $role): MemberModel
    {
        $this->role = $role;

        return $this;
    }

    public function getRole(): RoleModel
    {
        return $this->role;
    }

    public function getMetaData(): array
    {
        return \json_decode($this->metaData, true);
    }
}
