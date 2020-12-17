<?php

namespace App\Projects;

use App\Models\Model;

/**
 * Interface MetaDataElementModel
 *
 * @package App\Projects
 */
interface MetaDataElementModel extends Model
{
    public const PROPERTY_PROJECT  = 'project';
    public const PROPERTY_NAME     = 'name';
    public const PROPERTY_LABEL    = 'label';
    public const PROPERTY_REQUIRED = 'required';
    public const PROPERTY_IN_LIST  = 'inList';
    public const PROPERTY_TYPE     = 'type';

    /**
     * @return ProjectModel
     */
    public function getProject(): ProjectModel;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label): self;

    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param bool $required
     *
     * @return $this
     */
    public function setRequired(bool $required): self;

    /**
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * @param bool $inList
     *
     * @return $this
     */
    public function setInList(bool $inList): self;

    /**
     * @return bool
     */
    public function isInList(): bool;

    /**
     * @return array
     */
    public function toArray(): array;
}
