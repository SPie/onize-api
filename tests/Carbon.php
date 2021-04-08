<?php

namespace Tests;

use Carbon\CarbonImmutable;

/**
 * Trait Carbon
 *
 * @package Tests
 */
trait Carbon
{
    /**
     * @param CarbonImmutable $carbon
     *
     * @return $this
     */
    protected function setCarbonMock(CarbonImmutable $carbon): self
    {
        CarbonImmutable::setTestNow($carbon);

        return $this;
    }

    /**
     * @return $this
     */
    private function clearCarbonMock(): self
    {
        CarbonImmutable::setTestNow();

        return $this;
    }
}
