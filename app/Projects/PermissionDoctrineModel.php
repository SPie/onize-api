<?php

namespace App\Projects;

use App\Models\AbstractDoctrineModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="permissions")
 * @ORM\Entity(repositoryClass="App\Projects\PermissionDoctrineRepository")
 */
class PermissionDoctrineModel extends AbstractDoctrineModel implements PermissionModel
{
    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
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

    public function __construct(string $name, string $description, array $roles = [])
    {
        $this->name = $name;
        $this->description = $description;
        $this->roles = new ArrayCollection($roles);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setRoles(array $roles): PermissionModel
    {
        $this->roles = new ArrayCollection($roles);

        return $this;
    }

    public function addRole(RoleModel $role): PermissionModel
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    /**
     * @return RoleModel[]|Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }
}
