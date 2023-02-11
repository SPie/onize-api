<?php

namespace Tests\Helper;

use Illuminate\Testing\TestResponse;

trait ApiHelper
{
    private function doApiCall(string $method, string $uri, array $parameters = [], array $headers = [], array $cookies = []): TestResponse
    {
        $this->withCredentials();
        $this->withCookies($cookies);

        return $this->json(
            $method,
            $uri,
            $parameters,
            $headers
        );
    }
}
