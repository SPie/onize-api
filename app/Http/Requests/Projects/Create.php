<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Create
 *
 * @package App\Http\Requests\Projects
 */
class Create extends FormRequest
{
    public const PARAMETER_LABEL              = 'label';
    public const PARAMETER_DESCRIPTION        = 'description';
    public const PARAMETER_META_DATA_ELEMENTS = 'metaDataElements';
    public const PARAMETER_META_DATA          = 'metaData';

    public const META_DATA_ELEMENT_NAME     = 'name';
    public const META_DATA_ELEMENT_LABEL    = 'label';
    public const META_DATA_ELEMENT_REQUIRED = 'required';
    public const META_DATA_ELEMENT_IN_LIST  = 'inList';
    public const META_DATA_ELEMENT_TYPE     = 'type';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            self::PARAMETER_LABEL                                                         => ['required', 'string'],
            self::PARAMETER_DESCRIPTION                                                   => ['required', 'string'],
            self::PARAMETER_META_DATA_ELEMENTS                                            => ['present', 'array'],
            self::PARAMETER_META_DATA_ELEMENTS . '.*.' . self::META_DATA_ELEMENT_NAME     => ['required', 'string'],
            self::PARAMETER_META_DATA_ELEMENTS . '.*.' . self::META_DATA_ELEMENT_LABEL    => ['required', 'string'],
            self::PARAMETER_META_DATA_ELEMENTS . '.*.' . self::META_DATA_ELEMENT_REQUIRED => ['boolean'],
            self::PARAMETER_META_DATA_ELEMENTS . '.*.' . self::META_DATA_ELEMENT_IN_LIST  => ['boolean'],
            self::PARAMETER_META_DATA_ELEMENTS . '.*.' . self::META_DATA_ELEMENT_TYPE     => [
                'required',
                \sprintf('in:%s', \implode(',', ['email', 'string', 'date', 'numeric'])),
            ],
            self::PARAMETER_META_DATA                                                     => ['present', $this->getValidateMetaDataRule()],
        ];
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->get(self::PARAMETER_LABEL);
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->get(self::PARAMETER_DESCRIPTION);
    }

    /**
     * @return array
     */
    public function getMetaDataElements(): array
    {
        return $this->get(self::PARAMETER_META_DATA_ELEMENTS, []);
    }

    /**
     * @return array
     */
    public function getMetaData(): array
    {
        return $this->get(self::PARAMETER_META_DATA, []);
    }

    /**
     * @return \Closure
     */
    private function getValidateMetaDataRule(): \Closure
    {
        return function ($argument, $metaData, $fail): bool {
            if (!\is_array($metaData)) {
                $fail('validation.array');

                return false;
            }

            $metaDataElements = [];
            $errors = [];
            foreach ($this->getMetaDataElements() as $metaDataElement) {
                $metaDataElements[$metaDataElement['name']] = $metaDataElement['type'];

                if (
                    isset($metaDataElement['required'])
                    && $metaDataElement['required']
                    && empty($metaData[$metaDataElement['name']])
                ) {
                    $errors[$metaDataElement['name']] = ['validation.required'];
                }
            }

            foreach ($metaData as $name => $value) {
                $metaDataErrors = [];
                if (!isset($metaDataElements[$name])) {
                    $metaDataErrors[] = 'validation.not-existing';
                } elseif ($metaDataElements[$name] == 'string' && $this->isInvalidString($value)) {
                    $metaDataErrors[] = 'validation.string';
                } elseif ($metaDataElements[$name] == 'email' && $this->isInvalidEmail($value)) {
                    $metaDataErrors[] = 'validation.email';
                } elseif ($metaDataElements[$name] == 'numeric' && $this->isInvalidNumeric($value)) {
                    $metaDataErrors[] = 'validation.numeric';
                } elseif ($metaDataElements[$name] == 'date' && $this->isInvalidDate($value)) {
                    $metaDataErrors[] = 'validation.date';
                }

                if (!empty($metaDataErrors)) {
                    $errors[$name] = $metaDataErrors;
                }
            }

            if (!empty($errors)) {
                $this->getValidatorInstance()->getMessageBag()->merge([self::PARAMETER_META_DATA => $errors]);

                return false;
            }

            return true;
        };
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function isInvalidString($value): bool
    {
        return !$this->getValidatorInstance()->validateString(self::PARAMETER_META_DATA, $value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function isInvalidEmail($value): bool
    {
        return !$this->getValidatorInstance()->validateEmail(self::PARAMETER_META_DATA, $value, []);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function isInvalidNumeric($value): bool
    {
        return !$this->getValidatorInstance()->validateNumeric(self::PARAMETER_META_DATA, $value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function isInvalidDate($value): bool
    {
        return !$this->getValidatorInstance()->validateDate(self::PARAMETER_META_DATA, $value);
    }
}
