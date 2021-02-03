<?php

namespace App\Projects;

use App\Models\AbstractDoctrineModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PermissionDoctrineModel
 *
 * @ORM\Table(name="permissions")
 * @ORM\Entity(repositoryClass="App\Projects\PermissionDoctrineRepository")
 *
 * @package App\Projects
 */
final class PermissionDoctrineModel extends AbstractDoctrineModel implements PermissionModel
{
    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $name;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Projects\RoleDoctrineModel", inversedBy="permissions")
     * @ORM\JoinTable(name="roles_permissions",
     *     joinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *     )
     *
     * @var RoleModel[]|Collection
     */
    private Collection $roles;

    /**
     * PermissionDoctrineModel constructor.
     *
     * @param string $name
     * @param string $description
     * @param array  $roles
     */
    public function __construct(string $name, string $description, array $roles = [])
    {
        $this->name = $name;
        $this->description = $description;
        $this->roles = new ArrayCollection($roles);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param array $roles
     *
     * @return PermissionModel
     */
    public function setRoles(array $roles): PermissionModel
    {
        $this->roles = new ArrayCollection($roles);

        return $this;
    }

    /**
     * @param RoleModel $role
     *
     * @return PermissionModel
     */
    public function addRole(RoleModel $role): PermissionModel
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }
}
