<?php

namespace App\Models;

/**
 * Interface Uuidable
 *
 * @package App\Models
 */
interface UuidModel
{
    const PROPERTY_UUID = 'uuid';

    /**
     * @return string
     */
    public function getUuid(): string;
}
