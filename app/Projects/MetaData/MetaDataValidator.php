<?php

namespace App\Projects\MetaData;

interface MetaDataValidator
{
    public function isValidString($value): bool;

    public function isValidEmail($value): bool;

    public function isValidNumeric($value): bool;

    public function isValidDateTime($value): bool;
}
