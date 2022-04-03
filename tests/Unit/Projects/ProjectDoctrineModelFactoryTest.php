<?php

namespace Tests\Unit\Projects;

use App\Projects\ProjectDoctrineModel;
use App\Projects\ProjectDoctrineModelFactory;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

final class ProjectDoctrineModelFactoryTest extends TestCase
{
    use ProjectHelper;

    private function getProjectDoctrineModelFactory(): ProjectDoctrineModelFactory
    {
        return new ProjectDoctrineModelFactory();
    }

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

    public function testCreateWithMetaData(): void
    {
        $label = $this->getFaker()->word;
        $description = $this->getFaker()->words(3, true);
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];

        $this->assertEquals(
            new ProjectDoctrineModel($label, $description, $metaData),
            $this->getProjectDoctrineModelFactory()->create($label, $description, $metaData)
        );
    }

    //endregion
}
