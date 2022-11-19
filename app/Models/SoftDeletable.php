<?php

namespace App\Models;

interface SoftDeletable
{
    public const PROPERTY_DELETED_AT = 'deletedAt';

    public function setDeletedAt(?\DateTime $deletedAt): self;

    public function getDeletedAt(): ?\DateTime;

    public function restore(): self;

    public function isDeleted(): bool;
}
