<?php

namespace App\Projects;

use App\Models\Model;
use App\Models\SoftDeletable;
use App\Models\Timestampable;
use App\Models\UuidModel;
use Doctrine\Common\Collections\Collection;

interface ProjectModel extends Model, SoftDeletable, Timestampable, UuidModel
{
    public const PROPERTY_LABEL              = 'label';
    public const PROPERTY_DESCRIPTION        = 'description';
    public const PROPERTY_META_DATA          = 'metaData';
    public const PROPERTY_ROLES              = 'roles';
    public const PROPERTY_META_DATA_ELEMENTS = 'metaDataElements';

    public function setLabel(string $label): self;

    public function getLabel(): string;

    public function setDescription(string $description): self;

    public function getDescription(): string;

    public function setMetaData(array $metaData): self;

    public function getMetaData(): array;

    /**
     * @param RoleModel[] $roles
     */
    public function setRoles(array $roles): self;

    public function addRole(RoleModel $role): self;

    /**
     * @return RoleModel[]|Collection
     */
    public function getRoles(): Collection;

    /**
     * @param MetaDataElementModel[] $metaDataElements
     */
    public function setMetaDataElements(array $metaDataElements): self;

    public function addMetaDataElement(MetaDataElementModel $metaDataElement): self;

    /**
     * @return MetaDataElementModel[]|Collection
     */
    public function getMetaDataElements(): Collection;

    public function toArray(): array;

    /**
     * @return MemberModel[]|Collection
     */
    public function getMembers(): Collection;

    public function hasMemberWithEmail(string $email): bool;
}
