<?php

namespace Tests\Helper;

use App\Http\Requests\Users\Register;
use App\Http\Requests\Validators\UniqueUser;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Mockery as m;
use Mockery\MockInterface;

/**
 * Trait HttpHelper
 *
 * @package Tests\Helper
 */
trait HttpHelper
{
    /**
     * @return JsonResponse|MockInterface
     */
    private function createJsonResponse(): JsonResponse
    {
        return m::spy(JsonResponse::class);
    }

    /**
     * @return ResponseFactory|MockInterface
     */
    private function createResponseFactory(): ResponseFactory
    {
        return m::spy(ResponseFactory::class);
    }

    /**
     * @param ResponseFactory|MockInterface $responseFactory
     * @param JsonResponse                  $response
     * @param array|null                    $data
     * @param int|null                      $statusCode
     * @param array|null                    $headers
     * @param array|null                    $options
     *
     * @return $this
     */
    private function mockResponseFactoryJson(
        MockInterface $responseFactory,
        JsonResponse $response,
        array $data = null,
        int $statusCode = null,
        array $headers = null,
        array $options = null
    ): self {
        $arguments = [];
        if ($data !== null) {
            $arguments[] = $data;
        }
        if ($statusCode !== null) {
            $arguments[] = $statusCode;
        }
        if ($headers !== null) {
            $arguments[] = $headers;
        }
        if ($options !== null) {
            $arguments[] = $options;
        }

        $responseFactory
            ->shouldReceive('json')
            ->withArgs($arguments)
            ->andReturn($response);

        return $this;
    }

    /**
     * @param string|null $email
     * @param string|null $password
     * @param bool|null   $remember
     *
     * @return Register|MockInterface
     */
    private function createRegister(string $email = null, string $password = null, bool $remember = null): Register
    {
        $request = m::spy(Register::class);
        $request
            ->shouldReceive('getEmail')
            ->andReturn($email ?: $this->getFaker()->safeEmail)
            ->getMock()
            ->shouldReceive('getPassword')
            ->andReturn($password ?: $this->getFaker()->password)
            ->getMock()
            ->shouldReceive('shouldRemeber')
            ->andReturn($remember ?? $this->getFaker()->boolean);

        return $request;
    }

    /**
     * @return UniqueUser
     */
    private function createUniqueUser(): UniqueUser
    {
        return m::spy(UniqueUser::class);
    }
}
