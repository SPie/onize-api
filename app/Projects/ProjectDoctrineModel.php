<?php

namespace App\Projects;

use App\Models\AbstractDoctrineModel;
use App\Models\SoftDelete;
use App\Models\Timestamps;
use App\Models\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class ProjectDoctrineModel
 *
 * @ORM\Table(name="projects")
 * @ORM\Entity(repositoryClass="App\Projects\ProjectDoctrineRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @package App\Projects
 */
class ProjectDoctrineModel extends AbstractDoctrineModel implements ProjectModel
{
    use SoftDelete;
    use Timestamps;
    use Uuid;

    /**
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $label;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Projects\RoleDoctrineModel", mappedBy="project", cascade={"persist"})
     *
     * @var RoleModel[]|Collection
     */
    private Collection $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Projects\MetaDataElementDoctrineModel", mappedBy="project", cascade={"persist"})
     *
     * @var MetaDataElementModel[]|Collection
     */
    private Collection $metaDataElements;

    public function __construct(string $label, string $description)
    {
        $this->label = $label;
        $this->description = $description;
        $this->roles = new ArrayCollection();
        $this->metaDataElements = new ArrayCollection();
    }

    /**
     * @param string $label
     *
     * @return $this|ProjectModel
     */
    public function setLabel(string $label): ProjectModel
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $description
     *
     * @return $this|ProjectModel
     */
    public function setDescription(string $description): ProjectModel
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param RoleModel[] $roles
     *
     * @return $this|ProjectModel
     */
    public function setRoles(array $roles): ProjectModel
    {
        $this->roles = new ArrayCollection($roles);

        return $this;
    }

    /**
     * @param RoleModel $role
     *
     * @return $this|ProjectModel
     */
    public function addRole(RoleModel $role): ProjectModel
    {
        if (!$this->getRoles()->contains($role)) {
            $this->getRoles()->add($role);
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

    /**
     * @param MetaDataElementModel[] $metaDataElements
     *
     * @return $this|ProjectModel
     */
    public function setMetaDataElements(array $metaDataElements): ProjectModel
    {
        $this->metaDataElements = new ArrayCollection($metaDataElements);

        return $this;
    }

    /**
     * @param MetaDataElementModel $metaDataElement
     *
     * @return $this|ProjectModel
     */
    public function addMetaDataElement(MetaDataElementModel $metaDataElement): ProjectModel
    {
        if (!$this->getMetaDataElements()->add($metaDataElement)) {
            $this->getMetaDataElements()->contains($metaDataElement);
        }

        return $this;
    }

    /**
     * @return MetaDataElementModel[]|Collection
     */
    public function getMetaDataElements(): Collection
    {
        return $this->metaDataElements;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::PROPERTY_UUID               => $this->getUuid(),
            self::PROPERTY_LABEL              => $this->getLabel(),
            self::PROPERTY_DESCRIPTION        => $this->getDescription(),
            self::PROPERTY_META_DATA_ELEMENTS => $this->getMetaDataElements()
                ->map(fn (MetaDataElementModel $metaDataElement) => $metaDataElement->toArray())
                ->toArray(),
            self::PROPERTY_ROLES              => $this->getRoles()
                ->map(fn (RoleModel $role) => $role->toArray())
                ->toArray(),
        ];
    }

    /**
     * @return MemberModel[]|Collection
     */
    public function getMembers(): Collection
    {
        $members = new ArrayCollection([]);
        foreach ($this->getRoles() as $role) {
            foreach ($role->getMembers() as $member) {
                $members->add($member);
            }
        }

        return $members;
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function hasMemberWithEmail(string $email): bool
    {
        return $this->getRoles()->exists(
            fn (int $i, RoleModel $role) => $role->getMembers()->exists(
                fn (int $i, MemberModel $member) => $member->getUser()->getEmail() === $email
            )
        );
    }
}
