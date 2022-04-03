<?php

namespace Tests\Helper;

use Illuminate\Testing\TestResponse;

trait ApiHelper
{
    private function doApiCall(string $method, string $uri, array $parameters = [], array $headers = []): TestResponse
    {
        return $this->call(
            $method,
            $uri,
            $parameters,
            [],
            [],
            $this->transformHeadersToServerVars(\array_merge(['Accept' => 'application/json'], $headers))
        );
    }
}
