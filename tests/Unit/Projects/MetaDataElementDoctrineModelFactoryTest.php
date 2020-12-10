<?php

namespace Tests\Unit\Projects;

use App\Projects\MetaDataElementDoctrineModel;
use App\Projects\MetaDataElementDoctrineModelFactory;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class MetaDataElementDoctrineModelFactoryTest
 *
 * @package Tests\Unit\Projects
 */
final class MetaDataElementDoctrineModelFactoryTest extends TestCase
{
    use ProjectHelper;

    //region Tests

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $project = $this->createProjectModel();
        $name = $this->getFaker()->word;
        $label = $this->getFaker()->word;
        $type = $this->createRandomMetaDataElementType();
        $required = $this->getFaker()->boolean;
        $inList = $this->getFaker()->boolean;

        $this->assertEquals(
            new MetaDataElementDoctrineModel(
                $project,
                $name,
                $label,
                $type,
                $required,
                $inList
            ),
            $this->getMetaDataElementDoctrineModelFactory()->create($project, $name, $label, $type, $required, $inList)
        );
    }

    //endregion

    /**
     * @return MetaDataElementDoctrineModelFactory
     */
    private function getMetaDataElementDoctrineModelFactory(): MetaDataElementDoctrineModelFactory
    {
        return new MetaDataElementDoctrineModelFactory();
    }
}
