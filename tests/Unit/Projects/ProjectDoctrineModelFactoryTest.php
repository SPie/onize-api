<?php

namespace Tests\Unit\Projects;

use App\Models\UuidGenerator;
use App\Projects\ProjectDoctrineModel;
use App\Projects\ProjectDoctrineModelFactory;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class ProjectDoctrineModelFactoryTest
 *
 * @package Tests\Unit\Projects
 */
final class ProjectDoctrineModelFactoryTest extends TestCase
{
    use ModelHelper;
    use ProjectHelper;

    //region Tests

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $label = $this->getFaker()->word;
        $description = $this->getFaker()->words(3, true);
        $uuid = $this->getFaker()->uuid;
        $uuidGenerator = $this->createUuidGenerator($uuid);

        $this->assertEquals(
            new ProjectDoctrineModel($uuid, $label, $description),
            $this->getProjectDoctrineModelFactory($uuidGenerator)->create($label, $description)
        );
    }

    //endregion

    /**
     * @param UuidGenerator|null $uuidGenerator
     *
     * @return ProjectDoctrineModelFactory
     */
    private function getProjectDoctrineModelFactory(UuidGenerator $uuidGenerator = null): ProjectDoctrineModelFactory
    {
        return new ProjectDoctrineModelFactory($uuidGenerator ?: $this->createUuidGenerator());
    }
}
