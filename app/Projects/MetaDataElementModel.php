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
    const PROPERTY_PROJECT  = 'project';
    const PROPERTY_NAME     = 'name';
    const PROPERTY_LABEL    = 'label';
    const PROPERTY_REQUIRED = 'required';
    const PROPERTY_IN_LIST  = 'inList';
    const PROPERTY_TYPE     = 'type';

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
