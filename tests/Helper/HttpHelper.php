<?php

namespace Tests\Helper;

use App\Http\Requests\Auth\Authenticate;
use App\Http\Requests\Users\Register;
use App\Http\Requests\Users\UpdatePassword;
use App\Http\Requests\Validators\UniqueUser;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\Validation\Validator;
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
     * @return MessageBag|MockInterface
     */
    private function createMessageBag(): MessageBag
    {
        return m::spy(MessageBag::class);
    }

    /**
     * @return Validator|MockInterface
     */
    private function createValidator(): Validator
    {
        return m::spy(Validator::class);
    }

    /**
     * @param Validator|MockInterface $validator
     * @param MessageBag              $messageBag
     *
     * @return $this
     */
    private function mockValidatorGetMessageBag(MockInterface $validator, MessageBag $messageBag): self
    {
        $validator
            ->shouldReceive('getMessageBag')
            ->andReturn($messageBag);

        return $this;
    }

    /**
     * @param Validator|MockInterface $validator
     * @param bool                    $valid
     * @param mixed                   $value
     *
     * @return $this
     */
    private function mockValidatorValidateString(MockInterface $validator, bool $valid, string $attribute, $value): self
    {
        $validator
            ->shouldReceive('validateString')
            ->with($attribute, $value)
            ->andReturn($valid);

        return $this;
    }

    /**
     * @param Validator|MockInterface $validator
     * @param bool                    $valid
     * @param string                  $attribute
     * @param mixed                   $value
     * @param array                   $params
     *
     * @return $this
     */
    private function mockValidatorValidateEmail(
        MockInterface $validator,
        bool $valid,
        string $attribute,
        $value,
        array $params
    ): self
    {
        $validator
            ->shouldReceive('validateEmail')
            ->with($attribute, $value, $params)
            ->andReturn($valid);

        return $this;
    }

    /**
     * @param Validator|MockInterface $validator
     * @param bool                    $valid
     * @param string                  $attribute
     * @param mixed                   $value
     *
     * @return $this
     */
    private function mockValidatorValidateNumeric(MockInterface $validator, bool $valid, string $attribute, $value): self
    {
        $validator
            ->shouldReceive('validateNumeric')
            ->with($attribute, $value)
            ->andReturn($valid);

        return $this;
    }

    /**
     * @param Validator|MockInterface $validator
     * @param bool                    $valid
     * @param string                  $attribute
     * @param mixed                   $value
     *
     * @return $this
     */
    private function mockValidatorValidateDate(MockInterface $validator, bool $valid, string $attribute, $value): self
    {
        $validator
            ->shouldReceive('validateDate')
            ->with($attribute, $value)
            ->andReturn($valid);

        return $this;
    }

    /**
     * @return UniqueUser
     */
    private function createUniqueUser(): UniqueUser
    {
        return m::spy(UniqueUser::class);
    }

    /**
     * @param UniqueUser|MockInterface $uniqueUser
     * @param int|null                 $userId
     *
     * @return $this
     */
    private function mockUniqueUserSetExistingUserId(MockInterface $uniqueUser, ?int $userId): self
    {
        $uniqueUser
            ->shouldReceive('setExistingUserId')
            ->with($userId)
            ->andReturn($uniqueUser);

        return $this;
    }

    /**
     * @param UniqueUser|MockInterface $uniqueUser
     * @param int|null                 $userId
     *
     * @return $this
     */
    private function assertUniqueUserSetExistingUserId(MockInterface $uniqueUser, ?int $userId): self
    {
        $uniqueUser
            ->shouldHaveReceived('setExistingUserId')
            ->with($userId)
            ->once();

        return $this;
    }

    /**
     * @param string|null $email
     * @param string|null $password
     *
     * @return Authenticate|MockInterface
     */
    private function createAuthenticateRequest(string $email = null, string $password = null): Authenticate
    {
        return m::spy(Authenticate::class)
            ->shouldReceive('getEmail')
            ->andReturn($email ?: $this->getFaker()->safeEmail)
            ->getMock()
            ->shouldReceive('getPassword')
            ->andReturn($password ?: $this->getFaker()->password)
            ->getMock();
    }

    /**
     * @param string|null $password
     *
     * @return UpdatePassword|MockInterface
     */
    private function createUpdatePasswordRequest(string $password = null): UpdatePassword
    {
        return m::spy(UpdatePassword::class)
            ->shouldReceive('getUserPassword')
            ->andReturn($password)
            ->getMock();
    }
}
