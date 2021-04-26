<?php

namespace App\Projects\MetaData;

/**
 * Interface MetaDataValidator
 *
 * @package App\Projects\MetaData
 */
interface MetaDataValidator
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidString($value): bool;

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidEmail($value): bool;

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidNumeric($value): bool;

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidDateTime($value): bool;
}
