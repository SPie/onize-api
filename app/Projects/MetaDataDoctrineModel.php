<?php

namespace App\Projects;

use App\Models\AbstractDoctrineModel;
use App\Users\UserModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class MetaDataDoctrineModel
 *
 * @ORM\Table(name="meta_data")
 * @ORM\Entity(repositoryClass="App\Projects\MetaDataDoctrineRepository")
 *
 * @package App\Projects
 */
final class MetaDataDoctrineModel extends AbstractDoctrineModel implements MetaDataModel
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Projects\ProjectDoctrineModel", inversedBy="metaData", cascade={"persist"})
     *
     * @var ProjectModel
     */
    private ProjectModel $project;

    /**
     * @ORM\ManyToOne(targetEntity="App\Users\UserDoctrineModel", inversedBy="metaData", cascade={"persist"})
     *
     * @var UserModel
     */
    private UserModel $user;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $name;

    /**
     * @ORM\Column(name="value", type="string", length=255, nullable=false)
     *
     * @var string
     */
    private string $value;

    /**
     * MetaDataDoctrineModel constructor.
     *
     * @param ProjectModel $project
     * @param UserModel    $user
     * @param string       $name
     * @param string       $value
     */
    public function __construct(ProjectModel $project, UserModel $user, string $name, string $value)
    {
        $this->project = $project;
        $this->user = $user;
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return ProjectModel
     */
    public function getProject(): ProjectModel
    {
        return $this->project;
    }

    /**
     * @return UserModel
     */
    public function getUser(): UserModel
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $value
     *
     * @return $this|MetaDataModel
     */
    public function setValue(string $value): MetaDataModel
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
