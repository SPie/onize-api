<?php

namespace App\Models;

/**
 * Interface Uuidable
 *
 * @package App\Models
 */
interface UuidModel
{
    public const PROPERTY_UUID = 'uuid';

    /**
     * @return string
     */
    public function getUuid(): string;
}
