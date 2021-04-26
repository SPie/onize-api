<?php

namespace Tests\Unit\Projects;

use App\Models\DatabaseHandler;
use App\Projects\RoleDoctrineRepository;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class RoleDoctrineRepositoryTest
 *
 * @package Tests\Unit\Projects
 */
final class RoleDoctrineRepositoryTest extends TestCase
{
    use ModelHelper;
    use ProjectHelper;

    //region Tests

    /**
     * @return void
     */
    public function testFindOneByUuid(): void
    {
        $uuid = $this->getFaker()->uuid;
        $role = $this->createRoleModel();
        $databaseHandler = $this->createDatabaseHandler();
        $this->mockDatabaseHandlerLoad($databaseHandler, $role, ['uuid' => $uuid]);

        $this->assertEquals($role, $this->getRoleDoctrineRepository($databaseHandler)->findOneByUuid($uuid));
    }

    /**
     * @return void
     */
    public function testFindOneByUuidWithoutRole(): void
    {
        $uuid = $this->getFaker()->uuid;
        $databaseHandler = $this->createDatabaseHandler();
        $this->mockDatabaseHandlerLoad($databaseHandler, null, ['uuid' => $uuid]);

        $this->assertNull($this->getRoleDoctrineRepository($databaseHandler)->findOneByUuid($uuid));
    }

    //endregion

    /**
     * @param DatabaseHandler|null $databaseHandler
     *
     * @return RoleDoctrineRepository
     */
    private function getRoleDoctrineRepository(DatabaseHandler $databaseHandler = null): RoleDoctrineRepository
    {
        return new RoleDoctrineRepository($databaseHandler ?: $this->createDatabaseHandler());
    }
}
