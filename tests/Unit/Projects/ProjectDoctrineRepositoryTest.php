<?php

namespace Tests\Unit\Projects;

use App\Models\DatabaseHandler;
use App\Projects\ProjectDoctrineRepository;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class ProjectDoctrineRepositoryTest
 *
 * @package Tests\Unit\Projects
 */
final class ProjectDoctrineRepositoryTest extends TestCase
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
        $project = $this->createProjectModel();
        $databaseHandler = $this->createDatabaseHandler();
        $this->mockDatabaseHandlerLoad($databaseHandler, $project, ['uuid' => $uuid]);

        $this->assertEquals($project, $this->getProjectDoctrineRepository($databaseHandler)->findOneByUuid($uuid));
    }

    /**
     * @return void
     */
    public function testFindOneByUuidWithoutProject(): void
    {
        $uuid = $this->getFaker()->uuid;
        $databaseHandler = $this->createDatabaseHandler();
        $this->mockDatabaseHandlerLoad($databaseHandler, null, ['uuid' => $uuid]);

        $this->assertNull($this->getProjectDoctrineRepository($databaseHandler)->findOneByUuid($uuid));
    }

    //endregion

    /**
     * @param DatabaseHandler|null $databaseHandler
     *
     * @return ProjectDoctrineRepository
     */
    private function getProjectDoctrineRepository(DatabaseHandler $databaseHandler = null): ProjectDoctrineRepository
    {
        return new ProjectDoctrineRepository($databaseHandler ?: $this->createDatabaseHandler());
    }
}
