<?php

namespace App\Projects\MetaData;

use App\Projects\ProjectModel;

class MetaDataManager
{
    public function __construct(readonly private MetaDataValidator $metaDataValidator)
    {
    }

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
