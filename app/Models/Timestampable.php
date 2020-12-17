<?php

namespace App\Models;

/**
 * Interface Timestampable
 *
 * @package App\Models
 */
interface Timestampable
{
    public const PROPERTY_CREATED_AT = 'createdAt';
    public const PROPERTY_UPDATED_AT = 'updatedAt';

    /**
     * @param \DateTime|null $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(?\DateTime $createdAt);

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime;

    /**
     * @param \DateTime|null $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(?\DateTime $updatedAt);

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime;
}
