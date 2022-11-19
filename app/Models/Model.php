<?php

namespace App\Models;

interface Model
{
    public const PROPERTY_ID = 'id';

    public function setId(?int $id): self;

    public function getId(): ?int;
}
