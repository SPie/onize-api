<?php

namespace App\Models;

/**
 * Class AbstractDoctrineModel
 *
 * @package App\Models
 */
abstract class AbstractDoctrineModel implements Model
{
    /**
     * @var int|null
     */
    protected ?int $id;

    /**
     * @param int|null $id
     *
     * @return $this|Model
     */
    public function setId(?int $id): Model
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
