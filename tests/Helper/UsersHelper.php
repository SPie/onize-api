<?php

namespace Tests\Helper;

use App\Projects\ProjectModel;
use App\Projects\RoleModel;
use App\Users\UserDoctrineModel;
use App\Users\UserManager;
use App\Users\UserModel;
use App\Users\UserModelFactory;
use App\Users\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mockery as m;
use Mockery\MockInterface;

/**
 * Trait UsersHelper
 *
 * @package Tests\Helper
 */
trait UsersHelper
{
    /**
     * @return UserModel|MockInterface
     */
    private function createUserModel(): UserModel
    {
        return m::spy(UserModel::class);
    }

    /**
     * @param UserModel|MockInterface $user
     * @param array                   $data
     *
     * @return $this
     */
    private function mockUserModelToArray(MockInterface $user, array $data): self
    {
        $user
            ->shouldReceive('toArray')
            ->andReturn($data);

        return $this;
    }

    /**
     * @param UserModel|MockInterface $user
     * @param string                  $authPassword
     *
     * @return $this
     */
    private function mockUserModelGetAuthPassword(MockInterface $user, string $authPassword): self
    {
        $user
            ->shouldReceive('getAuthPassword')
            ->andReturn($authPassword);

        return $this;
    }

    /**
     * @param UserModel|MockInterface $user
     * @param string                  $email
     *
     * @return $this
     */
    private function mockUserModelSetEmail(MockInterface $user, string $email): self
    {
        $user
            ->shouldReceive('setEmail')
            ->with($email)
            ->andReturn($user);

        return $this;
    }

    /**
     * @param UserModel|MockInterface $user
     * @param string                  $email
     *
     * @return $this
     */
    private function assertUserModelSetEmail(MockInterface $user, string $email): self
    {
        $user
            ->shouldHaveReceived('setEmail')
            ->with($email)
            ->once();

        return $this;
    }

    /**
     * @param UserModel|MockInterface $user
     * @param string                  $password
     *
     * @return $this
     */
    private function mockUserModelSetPassword(MockInterface $user, string $password): self
    {
        $user
            ->shouldReceive('setPassword')
            ->with($password)
            ->andReturn($user);

        return $this;
    }

    /**
     * @param UserModel|MockInterface $user
     * @param string                  $password
     *
     * @return $this
     */
    private function assertUserModelSetPassword(MockInterface $user, string $password): self
    {
        $user
            ->shouldHaveReceived('setPassword')
            ->with($password)
            ->once();

        return $this;
    }

    /**
     * @param UserModel|MockInterface $user
     * @param RoleModel[]|Collection  $roles
     *
     * @return $this
     */
    private function mockUserModelGetRoles(MockInterface $user, Collection $roles): self
    {
        $user
            ->shouldReceive('getRoles')
            ->andReturn($roles);

        return $this;
    }

    /**
     * @param UserModel|MockInterface $userModel
     * @param bool          $isMember
     * @param ProjectModel  $project
     *
     * @return $this
     */
    private function mockUserModelIsMemberOfProject(MockInterface $userModel, bool $isMember, ProjectModel $project): self
    {
        $userModel
            ->shouldReceive('isMemberOfProject')
            ->with($project)
            ->andReturn($isMember);

        return $this;
    }

    /**
     * @param MockInterface $userModel
     * @param array         $memberData
     *
     * @return $this
     */
    private function mockUserModelMemberData(MockInterface $userModel, array $memberData): self
    {
        $userModel
            ->shouldReceive('memberData')
            ->andReturn($memberData);

        return $this;
    }

    /**
     * @param UserModel|MockInterface $userModel
     * @param array                   $metaData
     *
     * @return $this
     */
    private function mockUserModelSetMetaData(MockInterface $userModel, array $metaData): self
    {
        $userModel
            ->shouldReceive('setMetaData')
            ->with($metaData)
            ->andReturn($userModel)
            ->once();

        return $this;
    }

    /**
     * @param UserModel|MockInterface $userModel
     * @param RoleModel|null          $role
     * @param ProjectModel            $project
     *
     * @return $this
     */
    private function mockUserModelGetRoleForProject(MockInterface $userModel, ?RoleModel $role, ProjectModel $project): self
    {
        $userModel
            ->shouldReceive('getRoleForProject')
            ->with($project)
            ->andReturn($role);

        return $this;
    }

    /**
     * @param UserModel|MockInterface $userModel
     * @param string                  $email
     *
     * @return $this
     */
    private function mockUserModelGetEmail(MockInterface $userModel, string $email): self
    {
        $userModel
            ->shouldReceive('getEmail')
            ->andReturn($email);

        return $this;
    }

    /**
     * @return UserManager|MockInterface
     */
    private function createUserManager(): UserManager
    {
        return m::spy(UserManager::class);
    }

    /**
     * @param UserManager|MockInterface $userManager
     * @param UserModel|\Exception      $user
     * @param string                    $email
     * @param string                    $password
     *
     * @return $this
     */
    private function mockUserManagerCreateUser(MockInterface $userManager, $user, string $email, string $password): self
    {
        $userManager
            ->shouldReceive('createUser')
            ->with($email, $password)
            ->andThrow($user);

        return $this;
    }

    /**
     * @param UserManager|MockInterface $userManager
     * @param UserModel|\Exception      $user
     * @param int                       $id
     *
     * @return $this
     */
    private function mockUserManagerGetUserById(MockInterface $userManager, $user, int $id): self
    {
        $userManager
            ->shouldReceive('getUserById')
            ->with($id)
            ->andThrow($user);

        return $this;
    }

    /**
     * @param UserManager|MockInterface $userManager
     * @param UserModel|\Exception      $user
     * @param string                    $email
     *
     * @return $this
     */
    private function mockUserManagerGetUserByEmail(MockInterface $userManager, $user, string $email): self
    {
        $userManager
            ->shouldReceive('getUserByEmail')
            ->with($email)
            ->andThrow($user);

        return $this;
    }

    /**
     * @param MockInterface $userManager
     * @param UserModel     $updatedUser
     * @param UserModel     $user
     * @param string|null   $email
     *
     * @return $this
     */
    private function mockUserManagerUpdateUserData(
        MockInterface $userManager,
        UserModel $updatedUser,
        UserModel $user,
        ?string $email
    ): self {
        $userManager
            ->shouldReceive('updateUserData')
            ->with($user, $email)
            ->andReturn($updatedUser);

        return $this;
    }

    /**
     * @param UserManager|MockInterface $userManager
     * @param UserModel                 $updatedUser
     * @param UserModel                 $user
     * @param string|null               $password
     *
     * @return $this
     */
    private function mockUserManagerUpdatePassword(
        MockInterface $userManager,
        UserModel $updatedUser,
        UserModel $user,
        ?string $password
    ): self {
        $userManager
            ->shouldReceive('updatePassword')
            ->with($user, $password)
            ->andReturn($updatedUser);

        return $this;
    }

    /**
     * @return UserModelFactory|MockInterface
     */
    private function createUserModelFactory(): UserModelFactory
    {
        return m::spy(UserModelFactory::class);
    }

    /**
     * @param UserModelFactory|MockInterface $userModelFactory
     * @param UserModel                      $user
     * @param string                         $email
     * @param string                         $password
     *
     * @return $this
     */
    private function mockUserModelFactoryCreate(
        MockInterface $userModelFactory,
        UserModel $user,
        string $email,
        string $password
    ): self {
        $userModelFactory
            ->shouldReceive('create')
            ->with($email, $password)
            ->andReturn($user);

        return $this;
    }

    /**
     * @param UserModelFactory|MockInterface $userModelFactory
     * @param MockInterface                  $updatedUser
     * @param MockInterface                  $user
     * @param string                         $password
     *
     * @return $this
     */
    private function mockUserModelFactorySetPassword(
        MockInterface $userModelFactory,
        MockInterface $updatedUser,
        MockInterface $user,
        string $password
    ): self {
        $userModelFactory
            ->shouldReceive('setPassword')
            ->with($user, $password)
            ->andReturn($updatedUser);

        return $this;
    }

    /**
     * @return UserRepository|MockInterface
     */
    private function createUserRepository(): UserRepository
    {
        return m::spy(UserRepository::class);
    }

    /**
     * @param UserRepository|MockInterface $userRepository
     * @param UserModel|null               $user
     * @param string                       $email
     *
     * @return $this
     */
    private function mockUserRepositoryFindOneByEmail(MockInterface $userRepository, ?UserModel $user, string $email): self
    {
        $userRepository
            ->shouldReceive('findOneByEmail')
            ->with($email)
            ->andReturn($user);

        return $this;
    }

    /**
     * @param int   $times
     * @param array $attributes
     *
     * @return UserDoctrineModel[]|Collection
     */
    private function createUserEntities(int $times = 1, array $attributes = []): Collection
    {
        return $this->createModelEntities(UserDoctrineModel::class, $times, $attributes);
    }

    /**
     * @param RoleModel $role
     *
     * @return UserModel
     */
    private function createUserWithRole(RoleModel $role): UserModel
    {
        return $this->createUserEntities(1, [UserModel::PROPERTY_ROLES => new ArrayCollection([$role])])->first();
    }
}
