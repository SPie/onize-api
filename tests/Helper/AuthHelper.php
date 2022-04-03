<?php

namespace Tests\Helper;

use App\Auth\AuthManager;
use App\Auth\RefreshTokenModel;
use App\Auth\RefreshTokenModelFactory;
use App\Auth\RefreshTokenRepository;
use App\Users\UserModel;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Mockery as m;
use Mockery\CompositeExpectation;
use Mockery\MockInterface;

/**
 * Trait AuthHelper
 *
 * @package Tests\Helper
 */
trait AuthHelper
{
    /**
     * @return StatefulGuard|MockInterface
     */
    private function createStatefulGuard(): StatefulGuard
    {
        return m::spy(StatefulGuard::class);
    }

    /**
     * @param StatefulGuard|MockInterface $guard
     * @param UserModel                   $user
     *
     * @return $this
     */
    private function assertStatefulGuardLogin(MockInterface $guard, UserModel $user): self
    {
        $guard
            ->shouldHaveReceived('login')
            ->with($user)
            ->once();

        return $this;
    }

    /**
     * @param StatefulGuard|MockInterface $guard
     * @param Authenticatable|null        $user
     *
     * @return $this
     */
    private function mockStatefulGuardUser(MockInterface $guard, ?Authenticatable $user): self
    {
        $guard
            ->shouldReceive('user')
            ->andReturn($user);

        return $this;
    }

    private function mockStatefulGuardAttempt(
        MockInterface $guard,
        bool $authenticated,
        array $credentials,
        bool $remember
    ): self {
        $guard
            ->shouldReceive('attempt')
            ->with($credentials, $remember)
            ->andReturn($authenticated);

        return $this;
    }

    /**
     * @return AuthManager|MockInterface
     */
    private function createAuthManager(): AuthManager
    {
        return m::spy(AuthManager::class);
    }

    /**
     * @param AuthManager|MockInterface $authManager
     * @param UserModel                 $user
     *
     * @return $this
     */
    private function assertAuthManagerLogin(MockInterface $authManager, UserModel $user): self
    {
        $authManager
            ->shouldHaveReceived('login')
            ->with($user)
            ->once();

        return $this;
    }

    /**
     * @param AuthManager|MockInterface $authManager
     * @param UserModel|\Exception      $user
     *
     * @return $this
     */
    private function mockAuthManagerAuthenticatedUser(MockInterface $authManager, $user): self
    {
        $authManager
            ->shouldReceive('authenticatedUser')
            ->andThrow($user);

        return $this;
    }

    /**
     * @param AuthManager|MockInterface $authManager
     * @param UserModel|\Exception      $user
     * @param string                    $email
     * @param string                    $password
     *
     * @return $this
     */
    private function mockAuthManagerAuthenticate(
        MockInterface $authManager,
        $user,
        string $email,
        string $password
    ): self {
        $authManager
            ->shouldReceive('authenticate')
            ->with($email, $password)
            ->andThrow($user);

        return $this;
    }

    /**
     * @return Gate|MockInterface
     */
    private function createGate(): Gate
    {
        return m::spy(Gate::class);
    }

    /**
     * @param Gate|MockInterface      $gate
     * @param JsonResponse|\Exception $response
     * @param string                  $ability
     * @param array                   $arguments
     *
     * @return $this
     */
    private function mockGateAuthorize(Gate $gate, $response, string $ability, array $arguments): self
    {
        $gate
            ->shouldReceive('authorize')
            ->with($ability, $arguments)
            ->andThrow($response);

        return $this;
    }

    /**
     * @return RefreshTokenModel|MockInterface
     */
    private function createRefreshTokenModel(): RefreshTokenModel
    {
        return m::spy(RefreshTokenModel::class);
    }

    private function mockRefreshTokenIsRevoked(MockInterface $refreshTokenModel, bool $revoked): CompositeExpectation
    {
        return $refreshTokenModel
            ->shouldReceive('isRevoked')
            ->andReturn($revoked);
    }

    /**
     * @return RefreshTokenModelFactory|MockInterface
     */
    private function createRefreshTokenModelFactory(): RefreshTokenModelFactory
    {
        return m::spy(RefreshTokenModelFactory::class);
    }

    private function mockRefreshTokenModelFactoryCreate(
        MockInterface $refreshTokenModelFactory,
        RefreshTokenModel $refreshTokenModel,
        string $token
    ): CompositeExpectation {
        return $refreshTokenModelFactory
            ->shouldReceive('create')
            ->with($token)
            ->andReturn($refreshTokenModel);
    }

    /**
     * @return RefreshTokenRepository|MockInterface
     */
    private function createRefreshTokenRepository(): RefreshTokenRepository
    {
        return m::spy(RefreshTokenRepository::class);
    }

    private function mockRefreshTokenRepositoryFindOneByRefreshTokenId(
        MockInterface $refreshTokenRepository,
        ?RefreshTokenModel $refreshTokenModel,
        string $refreshTokenId
    ): CompositeExpectation {
        return $refreshTokenRepository
            ->shouldReceive('findOneByRefreshTokenId')
            ->with($refreshTokenId)
            ->andReturn($refreshTokenModel);
    }
}
