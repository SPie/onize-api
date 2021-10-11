<?php

namespace App\Models\Exceptions;

use Throwable;

final class ModelsNotFoundException extends ModelNotFoundException
{
    private array $identifiers;

    public function __construct(string $modelType, array $identifiers = [])
    {
        $this->identifiers = $identifiers;

        parent::__construct(\sprintf('%s with identifiers %s not found.', $modelType, \implode(',', $identifiers)));
    }

    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }
}
