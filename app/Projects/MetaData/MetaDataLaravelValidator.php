<?php

namespace App\Projects\MetaData;

final class MetaDataLaravelValidator implements MetaDataValidator
{
    public function __construct(readonly private LaravelAttributesValidator $validator)
    {
    }

    public function isValidString($value): bool
    {
        return $this->validator->validateString('', $value);
    }

    public function isValidEmail($value): bool
    {
        return $this->validator->validateEmail('', $value, []);
    }

    public function isValidNumeric($value): bool
    {
        return $this->validator->validateNumeric('', $value);
    }

    public function isValidDateTime($value): bool
    {
        return $this->validator->validateDate('', $value);
    }
}
