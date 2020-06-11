<?php

namespace Tests\Unit\Users;

use App\Models\DatabaseHandler;
use App\Users\UserDoctrineRepository;
use Tests\Helper\ModelHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class UserDoctrineRepositoryTest
 *
 * @package Tests\Unit\Users
 */
final class UserDoctrineRepositoryTest extends TestCase
{
    use ModelHelper;
    use UsersHelper;

    //region Tests

    /**
     * @param bool $withUser
     *
     * @return array
     */
    private function setUpFindOneByEmailTest(bool $withUser = true): array
    {
        $email = $this->getFaker()->safeEmail;
        $user = $this->createUserModel();
        $databaseHandler = $this->createDatabaseHandler();
        $this->mockDatabaseHandlerLoad($databaseHandler, $withUser ? $user : null, ['email' => $email]);
        $userDoctrineRepository = $this->getUserDoctrineRepository($databaseHandler);

        return [$userDoctrineRepository, $email, $user];
    }

    /**
     * @return void
     */
    public function testFindOneByEmail(): void
    {
        /** @var UserDoctrineRepository $userDoctrineRepository */
        [$userDoctrineRepository, $email, $user] = $this->setUpFindOneByEmailTest();

        $this->assertEquals($user, $userDoctrineRepository->findOneByEmail($email));
    }

    /**
     * @return void
     */
    public function testFindOneByEmailWithoutUser(): void
    {
        /** @var UserDoctrineRepository $userDoctrineRepository */
        [$userDoctrineRepository, $email] = $this->setUpFindOneByEmailTest(false);

        $this->assertNull($userDoctrineRepository->findOneByEmail($email));
    }

    //endregion

    /**
     * @param DatabaseHandler|null $databaseHandler
     *
     * @return UserDoctrineRepository
     */
    private function getUserDoctrineRepository(DatabaseHandler $databaseHandler = null): UserDoctrineRepository
    {
        return new UserDoctrineRepository($databaseHandler ?: $this->createDatabaseHandler());
    }

}
