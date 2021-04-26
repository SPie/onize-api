<?php

namespace App\Projects\MetaData;

use App\Projects\ProjectModel;

/**
 * Class MetaDataManager
 *
 * @package App\Projects\MetaData
 */
class MetaDataManager
{
    /**
     * @var MetaDataValidator
     */
    private MetaDataValidator $metaDataValidator;

    /**
     * MetaDataManager constructor.
     *
     * @param MetaDataValidator $metaDataValidator
     */
    public function __construct(MetaDataValidator $metaDataValidator)
    {
        $this->metaDataValidator = $metaDataValidator;
    }

    /**
     * @param ProjectModel $project
     * @param array        $metaData
     *
     * @return array
     */
    public function validateMetaData(ProjectModel $project, array $metaData): array
    {
        $validationErrors = [];

        $metaDataElements = [];
        foreach ($project->getMetaDataElements() as $metaDataElement) {
            $metaDataElements[$metaDataElement->getName()] = $metaDataElement;

            if ($metaDataElement->isRequired() && empty($metaData[$metaDataElement->getName()])) {
                $validationErrors[$metaDataElement->getName()] = ['required'];
            }
        }

        foreach ($metaData as $name => $value) {
            if (empty($metaDataElements[$name])) {
                $validationErrors[$name] = ['not-existing'];

                continue;
            }

            $error = $this->validateMetaDataType($metaDataElements[$name]->getType(), $value);
            if (!empty($error)) {
                $validationErrors[$name] = [$error];
            }
        }

        return $validationErrors;
    }

    /**
     * @param string $type
     * @param mixed  $value
     *
     * @return string|null
     */
    private function validateMetaDataType(string $type, $value): ?string
    {
        if ($type === 'string' && !$this->metaDataValidator->isValidString($value)) {
            return 'string';
        }
        if ($type === 'email' && !$this->metaDataValidator->isValidEmail($value)) {
            return 'email';
        }
        if ($type === 'numeric' && !$this->metaDataValidator->isValidNumeric($value)) {
            return 'numeric';
        }
        if ($type === 'date' && !$this->metaDataValidator->isValidDateTime($value)) {
            return 'date';
        }

        return null;
    }
}
