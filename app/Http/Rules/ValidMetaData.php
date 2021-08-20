<?php

namespace App\Http\Rules;

use App\Projects\MetaData\MetaDataManager;
use App\Projects\ProjectModel;
use Illuminate\Contracts\Validation\ImplicitRule;

class ValidMetaData implements ImplicitRule
{
    private ?ProjectModel $project;

    private string|array $message;

    public function __construct(private MetaDataManager $metaDataManager)
    {
        $this->project = null;
        $this->message = '';
    }

    public function setProject(ProjectModel $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function passes($attribute, $value)
    {
        if (!$this->project) {
            return true;
        }

        $value ??= [];
        if (!\is_array($value)) {
            $this->message = 'validation.array';

            return false;
        }

        $validationErrors = $this->metaDataManager->validateMetaData($this->project, $value);
        if (!empty($validationErrors)) {
            $errorMessages = [];
            foreach ($validationErrors as $metaDataName => $errors) {
                foreach ($errors as $error) {
                    $errorMessages[] = \sprintf('%s.validation.%s', $metaDataName, $error);
                }
            }

            $this->message = $errorMessages;

            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }
}
