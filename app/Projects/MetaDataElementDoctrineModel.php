<?php

namespace App\Projects;

use App\Models\AbstractDoctrineModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class MetaDataElementDoctrineModel
 *
 * @ORM\Table(name="meta_data_elements")
 * @ORM\Entity(repositoryClass="App\Projects\MetaDataElementDoctrineRepository")
 *
 * @package App\Projects
 */
final class MetaDataElementDoctrineModel extends AbstractDoctrineModel implements MetaDataElementModel
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Projects\ProjectDoctrineModel", inversedBy="metaDataElements", cascade={"persist"})
     *
     * @var ProjectModel
     */
    private ProjectModel $project;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $name;

    /**
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $label;

    /**
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $type;

    /**
     * @ORM\Column(name="required", type="bool")
     *
     * @var bool
     */
    private bool $required;

    /**
     * @ORM\Column(name="in_list", type="bool")
     *
     * @var bool
     */
    private bool $inList;

    /**
     * MetaDataElementDoctrineModel constructor.
     *
     * @param ProjectModel $project
     * @param string       $name
     * @param string       $label
     * @param string       $type
     * @param bool         $required
     * @param bool         $inList
     */
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

    /**
     * @return ProjectModel
     */
    public function getProject(): ProjectModel
    {
        return $this->project;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $label
     *
     * @return $this|MetaDataElementModel
     */
    public function setLabel(string $label): MetaDataElementModel
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
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param bool $required
     *
     * @return $this|MetaDataElementModel
     */
    public function setRequired(bool $required): MetaDataElementModel
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $inList
     *
     * @return $this|MetaDataElementModel
     */
    public function setInList(bool $inList): MetaDataElementModel
    {
        $this->inList = $inList;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInList(): bool
    {
        return $this->inList;
    }
}
