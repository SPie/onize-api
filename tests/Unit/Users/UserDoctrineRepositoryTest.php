<?php

namespace Tests\Unit\Users;

use App\Models\DatabaseHandler;
use App\Users\UserDoctrineRepository;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class UserDoctrineRepositoryTest extends TestCase
{
    use ModelHelper;
    use UsersHelper;

    private function getUserDoctrineRepository(DatabaseHandler $databaseHandler = null): UserDoctrineRepository
    {
        return new UserDoctrineRepository($databaseHandler ?: $this->createDatabaseHandler());
    }

    private function setUpFindOneByEmailTest(bool $withUser = true): array
    {
        $email = $this->getFaker()->safeEmail;
        $user = $this->createUserModel();
        $databaseHandler = $this->createDatabaseHandler();
        $this->mockDatabaseHandlerLoad($databaseHandler, $withUser ? $user : null, ['email' => $email]);
        $userDoctrineRepository = $this->getUserDoctrineRepository($databaseHandler);

        return [$userDoctrineRepository, $email, $user];
    }

    public function testFindOneByEmail(): void
    {
        /** @var UserDoctrineRepository $userDoctrineRepository */
        [$userDoctrineRepository, $email, $user] = $this->setUpFindOneByEmailTest();

        $this->assertEquals($user, $userDoctrineRepository->findOneByEmail($email));
    }

    public function testFindOneByEmailWithoutUser(): void
    {
        /** @var UserDoctrineRepository $userDoctrineRepository */
        [$userDoctrineRepository, $email] = $this->setUpFindOneByEmailTest(false);

        $this->assertNull($userDoctrineRepository->findOneByEmail($email));
    }

    private function setUpFindOneByUuidTest(bool $withUser = true): array
    {
        $uuid = $this->getFaker()->uuid;
        $user = $this->createUserModel();
        $databaseHandler = $this->createDatabaseHandler();
        $this->mockDatabaseHandlerLoad($databaseHandler, $withUser ? $user : null, ['uuid' => $uuid]);
        $userDoctrineRepository = $this->getUserDoctrineRepository($databaseHandler);

        return [$userDoctrineRepository, $uuid, $user];
    }

    public function testFindOneByUuid(): void
    {
        /** @var UserDoctrineRepository $userDoctrineRepository */
        [$userDoctrineRepository, $uuid, $user] = $this->setUpFindOneByUuidTest();

        $this->assertEquals($user, $userDoctrineRepository->findOneByUuid($uuid));
    }

    public function testFindOneByUuidWithoutUser(): void
    {
        /** @var UserDoctrineRepository $userDoctrineRepository */
        [$userDoctrineRepository, $uuid] = $this->setUpFindOneByUuidTest(withUser: false);

        $this->assertNull($userDoctrineRepository->findOneByUuid($uuid));
    }
}
