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
 * @ORM\Table(name="projects")
 * @ORM\Entity(repositoryClass="App\Projects\ProjectDoctrineRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ProjectDoctrineModel extends AbstractDoctrineModel implements ProjectModel
{
    use SoftDelete;
    use Timestamps;
    use Uuid;

    /**
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     */
    private string $label;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private string $description;

    /**
     * @ORM\Column(name="meta_data", type="json", nullable=false)
     */
    private array $metaData;

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

    public function __construct(string $label, string $description, array $metaData = [])
    {
        $this->label = $label;
        $this->description = $description;
        $this->metaData = $metaData;
        $this->roles = new ArrayCollection();
        $this->metaDataElements = new ArrayCollection();
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setMetaData(array $metaData): self
    {
        $this->metaData = $metaData;

        return $this;
    }

    public function getMetaData(): array
    {
        return $this->metaData;
    }

    /**
     * @param RoleModel[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = new ArrayCollection($roles);

        return $this;
    }

    public function addRole(RoleModel $role): self
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
     */
    public function setMetaDataElements(array $metaDataElements): self
    {
        $this->metaDataElements = new ArrayCollection($metaDataElements);

        return $this;
    }

    public function addMetaDataElement(MetaDataElementModel $metaDataElement): self
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

    public function toArray(): array
    {
        return [
            self::PROPERTY_UUID               => $this->getUuid(),
            self::PROPERTY_LABEL              => $this->getLabel(),
            self::PROPERTY_DESCRIPTION        => $this->getDescription(),
            self::PROPERTY_META_DATA          => $this->getMetaData(),
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

    public function hasMemberWithEmail(string $email): bool
    {
        return $this->getRoles()->exists(
            fn (int $i, RoleModel $role) => $role->getMembers()->exists(
                fn (int $i, MemberModel $member) => $member->getUser()->getEmail() === $email
            )
        );
    }
}
