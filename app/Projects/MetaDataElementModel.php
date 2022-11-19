<?php

namespace App\Projects;

use App\Models\Model;

interface MetaDataElementModel extends Model
{
    public const PROPERTY_PROJECT  = 'project';
    public const PROPERTY_NAME     = 'name';
    public const PROPERTY_LABEL    = 'label';
    public const PROPERTY_REQUIRED = 'required';
    public const PROPERTY_IN_LIST  = 'inList';
    public const PROPERTY_TYPE     = 'type';

    public function getProject(): ProjectModel;

    public function getName(): string;

    public function setLabel(string $label): self;

    public function getLabel(): string;

    public function getType(): string;

    public function setRequired(bool $required): self;

    public function isRequired(): bool;

    public function setInList(bool $inList): self;

    public function isInList(): bool;

    public function toArray(): array;
}
