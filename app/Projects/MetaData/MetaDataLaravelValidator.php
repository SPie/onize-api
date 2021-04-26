<?php

namespace App\Projects\MetaData;

use Illuminate\Validation\Validator;

/**
 * Class MetaDataLaravelValidator
 *
 * @package App\Projects\MetaData
 */
final class MetaDataLaravelValidator implements MetaDataValidator
{
    /**
     * @var LaravelAttributesValidator
     */
    private LaravelAttributesValidator $validator;

    /**
     * MetaDataLaravelValidator constructor.
     *
     * @param LaravelAttributesValidator $validator
     */
    public function __construct(LaravelAttributesValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidString($value): bool
    {
        return $this->validator->validateString('', $value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidEmail($value): bool
    {
        return $this->validator->validateEmail('', $value, []);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidNumeric($value): bool
    {
        return $this->validator->validateNumeric('', $value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidDateTime($value): bool
    {
        return $this->validator->validateDate('', $value);
    }
}
