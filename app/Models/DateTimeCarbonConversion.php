<?php

namespace App\Models;

use Carbon\CarbonImmutable;

trait DateTimeCarbonConversion
{
    /**
     * @param \DateTime|null $dateTime
     *
     * @return CarbonImmutable|null
     */
    private function convertDateTime(?\DateTime $dateTime): ?CarbonImmutable
    {
        return $dateTime ? new CarbonImmutable($dateTime) : null;
    }

    /**
     * @param CarbonImmutable|null $carbon
     *
     * @return \DateTime|null
     */
    private function convertCarbon(?CarbonImmutable $carbon): ?\DateTime
    {
        return $carbon ? $carbon->toDateTime() : null;
    }
}
