<?php

namespace Tests\Helper;

use Illuminate\Testing\TestResponse;

/**
 * Trait ApiHelper
 *
 * @package Tests\Helper
 */
trait ApiHelper
{
    /**
     * @param string $method
     * @param string $uri
     * @param array  $parameters
     *
     * @return TestResponse
     */
    private function doApiCall(string $method, string $uri, array $parameters = []): TestResponse
    {
        return $this->call(
            $method,
            $uri,
            $parameters,
            [],
            [],
            $this->transformHeadersToServerVars(['Accept' => 'application/json'])
        );
    }
}
