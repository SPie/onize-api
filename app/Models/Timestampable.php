<?php

namespace App\Models;

interface Timestampable
{
    public const PROPERTY_CREATED_AT = 'createdAt';
    public const PROPERTY_UPDATED_AT = 'updatedAt';

    public function setCreatedAt(?\DateTime $createdAt): self;

    public function getCreatedAt(): ?\DateTime;

    public function setUpdatedAt(?\DateTime $updatedAt): self;

    public function getUpdatedAt(): ?\DateTime;
}
