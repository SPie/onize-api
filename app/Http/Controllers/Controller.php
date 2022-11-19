<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct(private ResponseFactory $responseFactory)
    {
    }

    protected function getResponseFactory(): ResponseFactory
    {
        return $this->responseFactory;
    }
}
