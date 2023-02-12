<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    public const PARAMETER_LABEL              = 'label';
    public const PARAMETER_DESCRIPTION        = 'description';
    public const PARAMETER_PROJECT_META_DATA  = 'projectMetaData';
    public const PARAMETER_META_DATA_ELEMENTS = 'metaDataElements';
    public const PARAMETER_META_DATA          = 'metaData';

    public const META_DATA_ELEMENT_NAME     = 'name';
    public const META_DATA_ELEMENT_LABEL    = 'label';
    public const META_DATA_ELEMENT_REQUIRED = 'required';
    public const META_DATA_ELEMENT_IN_LIST  = 'inList';
    public const META_DATA_ELEMENT_TYPE     = 'type';

    public function rules(): array
    {
        return [
            self::PARAMETER_LABEL                                                         => ['required', 'string'],
            self::PARAMETER_DESCRIPTION                                                   => ['required', 'string'],
            self::PARAMETER_PROJECT_META_DATA                                             => ['array'],
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

    public function getLabel(): string
    {
        return $this->get(self::PARAMETER_LABEL);
    }

    public function getDescription(): string
    {
        return $this->get(self::PARAMETER_DESCRIPTION);
    }

    public function getProjectMetaData(): array
    {
        return $this->get(self::PARAMETER_PROJECT_META_DATA, []);
    }

    public function getMetaDataElements(): array
    {
        return $this->get(self::PARAMETER_META_DATA_ELEMENTS, []);
    }

    public function getMetaData(): array
    {
        return $this->get(self::PARAMETER_META_DATA, []);
    }

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
                    $errors[] = $this->buildErrorMessage($metaDataElement['name'], 'validation.required');
                }
            }

            foreach ($metaData as $name => $value) {
                if (!isset($metaDataElements[$name])) {
                    $errors[] = $this->buildErrorMessage($name, 'validation.not-existing');
                } elseif ($metaDataElements[$name] == 'string' && $this->isInvalidString($value)) {
                    $errors[] = $this->buildErrorMessage($name, 'validation.string');
                } elseif ($metaDataElements[$name] == 'email' && $this->isInvalidEmail($value)) {
                    $errors[] = $this->buildErrorMessage($name, 'validation.email');
                } elseif ($metaDataElements[$name] == 'numeric' && $this->isInvalidNumeric($value)) {
                    $errors[] = $this->buildErrorMessage($name, 'validation.numeric');
                } elseif ($metaDataElements[$name] == 'date' && $this->isInvalidDate($value)) {
                    $errors[] = $this->buildErrorMessage($name, 'validation.date');
                }
            }

            if (!empty($errors)) {
                $this->getValidatorInstance()->getMessageBag()->merge([self::PARAMETER_META_DATA => $errors]);

                return false;
            }

            return true;
        };
    }

    private function buildErrorMessage(string $fieldName, string $validationMessage): string
    {
        return \sprintf('%s.%s', $fieldName, $validationMessage);
    }

    private function isInvalidString($value): bool
    {
        return !$this->getValidatorInstance()->validateString(self::PARAMETER_META_DATA, $value);
    }

    private function isInvalidEmail($value): bool
    {
        return !$this->getValidatorInstance()->validateEmail(self::PARAMETER_META_DATA, $value, []);
    }

    private function isInvalidNumeric($value): bool
    {
        return !$this->getValidatorInstance()->validateNumeric(self::PARAMETER_META_DATA, $value);
    }

    private function isInvalidDate($value): bool
    {
        return !$this->getValidatorInstance()->validateDate(self::PARAMETER_META_DATA, $value);
    }
}
