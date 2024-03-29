<?php

namespace App\Models;

interface UuidModel
{
    public const PROPERTY_UUID = 'uuid';

    public function setUuid(string $uuid): self;

    public function getUuid(): ?string;
}
