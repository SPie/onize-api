<?php

namespace Tests\Unit\Projects;

use App\Projects\MetaDataElementDoctrineModel;
use App\Projects\ProjectModel;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class MetaDataElementDoctrineModelTest
 *
 * @package Tests\Unit\Projects
 */
final class MetaDataElementDoctrineModelTest extends TestCase
{
    use ProjectHelper;

    //region Tests

    /**
     * @return void
     */
    public function testToArray(): void
    {
        $metaDataElement = $this->getMetaDataElementDoctrineModel();

        $this->assertEquals(
            [
                'name'     => $metaDataElement->getName(),
                'label'    => $metaDataElement->getLabel(),
                'type'     => $metaDataElement->getType(),
                'required' => $metaDataElement->isRequired(),
                'inList'   => $metaDataElement->isInList(),
            ],
            $metaDataElement->toArray()
        );
    }

    //endregion

    /**
     * @param ProjectModel|null $project
     * @param string|null       $name
     * @param string|null       $label
     * @param string|null       $type
     * @param bool|null         $required
     * @param bool|null         $inList
     *
     * @return MetaDataElementDoctrineModel
     */
    private function getMetaDataElementDoctrineModel(
        ProjectModel $project = null,
        string $name = null,
        string $label = null,
        string $type = null,
        bool $required = null,
        bool $inList = null
    ): MetaDataElementDoctrineModel {
        return new MetaDataElementDoctrineModel(
            $project ?: $this->createProjectModel(),
            $name ?: $this->getFaker()->word,
            $label ?: $this->getFaker()->word,
            $type ?: $this->createRandomMetaDataElementType(),
            $required ?? $this->getFaker()->boolean,
            $inList ?? $this->getFaker()->boolean
        );
    }
}
