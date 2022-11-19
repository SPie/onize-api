<?php

namespace App\Models;

use Ramsey\Uuid\UuidFactoryInterface;

final class RamseyUuidGenerator implements UuidGenerator
{
    public function __construct(readonly private UuidFactoryInterface $uuidFactory)
    {
    }

    public function generate(): string
    {
        return $this->uuidFactory->uuid4()->toString();
    }
}
