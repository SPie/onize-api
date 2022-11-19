<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

trait SoftDelete
{
    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     *
     * @var \DateTime|null
     */
    private ?\DateTime $deletedAt;

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function restore(): self
    {
        $this->deletedAt = null;

        return $this;
    }

    public function isDeleted(): bool
    {
        return ($this->getDeletedAt() && (new \DateTime()) >= $this->getDeletedAt());
    }
}
