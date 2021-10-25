<?php

namespace Tests\Helper;

use App\Http\Requests\Auth\Authenticate;
use App\Http\Requests\Projects\ChangeRole;
use App\Http\Requests\Projects\RemoveRole;
use App\Http\Requests\Users\UpdatePassword;
use App\Http\Rules\PermissionsExist;
use App\Http\Rules\ProjectExists;
use App\Http\Rules\RoleExists;
use App\Http\Rules\UniqueUser;
use App\Http\Rules\UserExistsAndIsMember;
use App\Http\Rules\ValidMetaData;
use App\Projects\ProjectModel;
use App\Projects\RoleModel;
use App\Users\UserModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
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
     * @return Request|MockInterface
     */
    private function createRequest(): Request
    {
        return m::spy(Request::class);
    }

    /**
     * @param Request|MockInterface $request
     * @param Route                 $route
     *
     * @return $this
     */
    private function mockRequestRoute(MockInterface $request, Route $route): self
    {
        $request
            ->shouldReceive('route')
            ->andReturn($route);

        return $this;
    }

    /**
     * @return Route|MockInterface
     */
    private function createRoute(): Route
    {
        return m::spy(Route::class);
    }

    /**
     * @param Route|MockInterface $route
     * @param mixed               $parameter
     * @param string              $name
     * @param mixed               $default
     *
     * @return $this
     */
    private function mockRouteParameter(MockInterface $route, $parameter, string $name, $default): self
    {
        $route
            ->shouldReceive('parameter')
            ->with($name, $default)
            ->andReturn($parameter);

        return $this;
    }

    /**
     * @return Registrar|MockInterface
     */
    private function createRouter(): Registrar
    {
        return m::spy(Registrar::class);
    }

    /**
     * @param Registrar|MockInterface $router
     * @param Route                   $route
     *
     * @return $this
     */
    private function mockRouterSubstituteBindings(MockInterface $router, Route $route): self
    {
        $router
            ->shouldReceive('substituteBindings')
            ->with($route)
            ->once();

        return $this;
    }

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
    ): self {
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

    /**
     * @return RoleExists|MockInterface
     */
    private function createRoleExistsRule(): RoleExists
    {
        return m::spy(RoleExists::class);
    }

    private function mockRoleExistsRuleGetRole(MockInterface $roleExists, ?RoleModel $role): self
    {
        $roleExists
            ->shouldReceive('getRole')
            ->andReturn($role);

        return $this;
    }

    private function assertRoleExistsRuleSetProject(MockInterface $roleExists, ProjectModel $project): self
    {
        $roleExists
            ->shouldHaveReceived('setProject')
            ->with($project)
            ->once();

        return $this;
    }

    /**
     * @return ValidMetaData|MockInterface
     */
    private function createValidMetaDataRule(): ValidMetaData
    {
        return m::spy(ValidMetaData::class);
    }

    private function assertValidMetaDataRuleSetProject(MockInterface $validMetaDataRule, ProjectModel $project): self
    {
        $validMetaDataRule
            ->shouldHaveReceived('setProject')
            ->with($project)
            ->once();

        return $this;
    }

    /**
     * @return ChangeRole|MockInterface
     */
    private function createChangeRoleRequest(UserModel $user, RoleModel $role): ChangeRole
    {
        return m::spy(ChangeRole::class)
            ->shouldReceive('getUser')
            ->andReturn($user)
            ->getMock()
            ->shouldReceive('getRole')
            ->andReturn($role)
            ->getMock();
    }

    /**
     * @return UserExistsAndIsMember|MockInterface
     */
    private function createUserExistsAndIsMemberRule(): UserExistsAndIsMember
    {
        return m::spy(UserExistsAndIsMember::class);
    }

    private function assertUserExistsAndIsMemberRuleSetProject(MockInterface $rule, ProjectModel $project): self
    {
        $rule
            ->shouldHaveReceived('setProject')
            ->with($project)
            ->once();

        return $this;
    }

    private function mockUserExistsAndIsMemberRuleGetUser(MockInterface $rule, UserModel $user): self
    {
        $rule
            ->shouldReceive('getUser')
            ->andReturn($user);

        return $this;
    }

    /**
     * @return ProjectExists|MockInterface
     */
    private function createProjectExistsRule(ProjectModel $project = null): ProjectExists
    {
        return m::spy(ProjectExists::class)
            ->shouldReceive('getProject')
            ->andReturn($project ?: $this->createProjectModel())
            ->getMock();
    }

    /**
     * @return PermissionsExist|MockInterface
     */
    private function createPermissionsExistRule(Collection $permissions = null): PermissionsExist
    {
        return m::spy(PermissionsExist::class)
            ->shouldReceive('getPermissions')
            ->andReturn($permissions ?: new ArrayCollection([]))
            ->getMock();
    }

    /**
     * @return RemoveRole|MockInterface
     */
    private function createRemoveRoleRequest(RoleModel $newRole = null): RemoveRole
    {
        return m::spy(RemoveRole::class)
            ->shouldReceive('getNewRole')
            ->andReturn($newRole)
            ->getMock();
    }
}
