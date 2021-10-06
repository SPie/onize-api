<?php

namespace Tests\Unit\Projects;

use App\Models\DatabaseHandler;
use App\Projects\PermissionDoctrineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

final class PermissionDoctrineRepositoryTest extends TestCase
{
    use ModelHelper;
    use ProjectHelper;

    private function getPermissionDoctrineRepository(DatabaseHandler $databaseHandler = null): PermissionDoctrineRepository
    {
        return new PermissionDoctrineRepository($databaseHandler ?: $this->createDatabaseHandler());
    }

    public function testFindByNames(): void
    {
        $name = $this->getFaker()->word;
        $permission = $this->createPermissionModel();
        $databaseHandler = $this->createDatabaseHandler();
        $this->mockDatabaseHandlerLoadAll(
            $databaseHandler,
            new ArrayCollection([$permission]), ['name' => [$name]],
            null,
            null,
            null
        );

        $this->assertEquals(
            new ArrayCollection([$permission]),
            $this->getPermissionDoctrineRepository($databaseHandler)->findByNames([$name])
        );
    }
}
