<?php

namespace App\Projects;

use App\Models\AbstractDoctrineModel;
use App\Models\SoftDelete;
use App\Models\Timestamps;
use App\Users\UserModel;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class MemberDoctrineModel
 *
 * @ORM\Table(name="members")
 * @ORM\Entity(repositoryClass="App\Projects\MemerDoctrineRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @package App\Projects
 */
final class MemberDoctrineModel extends AbstractDoctrineModel implements MemberModel
{
    use SoftDelete;
    use Timestamps;

    /**
     * @ORM\ManyToOne(targetEntity="App\Users\UserDoctrineModel", inversedBy="members", cascade={"persist"})
     *
     * @var UserModel
     */
    private UserModel $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Projects\RoleDoctrineModel", inversedBy="members", cascade={"persist"})
     *
     * @var RoleModel
     */
    private RoleModel $role;

    /**
     * @ORM\Column(name="meta_data", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $metaData;

    /**
     * MemberDoctrineModel constructor.
     *
     * @param UserModel $user
     * @param RoleModel $role
     * @param array     $metaData
     */
    public function __construct(UserModel $user, RoleModel $role, array $metaData = [])
    {
        $this->user = $user;
        $this->role = $role;
        $this->metaData = \json_encode($metaData);
    }

    /**
     * @return UserModel
     */
    public function getUser(): UserModel
    {
        return $this->user;
    }

    /**
     * @return RoleModel
     */
    public function getRole(): RoleModel
    {
        return $this->role;
    }

    /**
     * @return array
     */
    public function getMetaData(): array
    {
        return \json_decode($this->metaData, true);
    }
}
