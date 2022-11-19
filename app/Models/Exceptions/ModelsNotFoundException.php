<?php

namespace App\Models\Exceptions;

final class ModelsNotFoundException extends ModelNotFoundException
{
    public function __construct(string $modelType, private array $identifiers = [])
    {
        parent::__construct(\sprintf('%s with identifiers %s not found.', $modelType, \implode(',', $identifiers)));
    }

    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }
}
