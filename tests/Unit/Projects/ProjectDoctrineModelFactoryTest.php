<?php

namespace Tests\Unit\Projects;

use App\Projects\ProjectDoctrineModel;
use App\Projects\ProjectDoctrineModelFactory;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class ProjectDoctrineModelFactoryTest
 *
 * @package Tests\Unit\Projects
 */
final class ProjectDoctrineModelFactoryTest extends TestCase
{
    use ProjectHelper;

    //region Tests

    public function testCreate(): void
    {
        $label = $this->getFaker()->word;
        $description = $this->getFaker()->words(3, true);

        $this->assertEquals(
            new ProjectDoctrineModel($label, $description),
            $this->getProjectDoctrineModelFactory()->create($label, $description)
        );
    }

    //endregion

    private function getProjectDoctrineModelFactory(): ProjectDoctrineModelFactory
    {
        return new ProjectDoctrineModelFactory();
    }
}
