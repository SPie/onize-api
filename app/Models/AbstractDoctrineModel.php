<?php

namespace App\Models;

use Doctrine\ORM\Mapping;

abstract class AbstractDoctrineModel implements Model
{
    /**
     * @Mapping\Id
     * @Mapping\GeneratedValue
     * @Mapping\Column(type="integer")
     *
     * @var int|null
     */
    protected ?int $id;

    public function setId(?int $id): Model
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
