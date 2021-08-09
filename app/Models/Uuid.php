<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait Uuid
 *
 * @package App\Models
 */
trait Uuid
{
    /**
     * @ORM\Column(name="uuid", type="string", length=255, nullable=false)
     *
     * @var string|null
     */
    private ?string $uuid = null;

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }
}
