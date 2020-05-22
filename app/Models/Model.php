<?php

namespace App\Models;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Interface Model
 *
 * @package App\Models
 */
interface Model extends Arrayable
{
    const PROPERTY_ID = 'id';

    /**
     * @param int|null $id
     *
     * @return $this
     */
    public function setId(?int $id): self;

    /**
     * @return int|null
     */
    public function getId(): ?int;
}
