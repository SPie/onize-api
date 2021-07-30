<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Controller constructor.
     *
     * @param ResponseFactory $responseFactory
     */
    public function __construct(private ResponseFactory $responseFactory)
    {
    }

    /**
     * @return ResponseFactory
     */
    protected function getResponseFactory(): ResponseFactory
    {
        return $this->responseFactory;
    }
}
