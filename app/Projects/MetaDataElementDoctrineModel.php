<?php

namespace App\Projects;

use App\Models\AbstractDoctrineModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="meta_data_elements")
 * @ORM\Entity(repositoryClass="App\Projects\MetaDataElementDoctrineRepository")
 */
class MetaDataElementDoctrineModel extends AbstractDoctrineModel implements MetaDataElementModel
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Projects\ProjectDoctrineModel", inversedBy="metaDataElements", cascade={"persist"})
     */
    private ProjectModel $project;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     */
    private string $label;

    /**
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private string $type;

    /**
     * @ORM\Column(name="required", type="boolean")
     */
    private bool $required;

    /**
     * @ORM\Column(name="in_list", type="boolean")
     */
    private bool $inList;

    public function __construct(
        ProjectModel $project,
        string $name,
        string $label,
        string $type,
        bool $required = false,
        bool $inList = false
    ) {
        $this->project = $project;
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->required = $required;
        $this->inList = $inList;
    }

    public function getProject(): ProjectModel
    {
        return $this->project;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setLabel(string $label): MetaDataElementModel
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setRequired(bool $required): MetaDataElementModel
    {
        $this->required = $required;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setInList(bool $inList): MetaDataElementModel
    {
        $this->inList = $inList;

        return $this;
    }

    public function isInList(): bool
    {
        return $this->inList;
    }

    public function toArray(): array
    {
        return [
            self::PROPERTY_NAME     => $this->getName(),
            self::PROPERTY_LABEL    => $this->getLabel(),
            self::PROPERTY_TYPE     => $this->getType(),
            self::PROPERTY_REQUIRED => $this->isRequired(),
            self::PROPERTY_IN_LIST  => $this->isInList(),
        ];
    }
}
