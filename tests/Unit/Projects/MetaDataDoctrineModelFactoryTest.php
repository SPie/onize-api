<?php

namespace Tests\Unit\Projects;

use App\Projects\MetaDataDoctrineModel;
use App\Projects\MetaDataDoctrineModelFactory;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

/**
 * Class MetaDataDoctrineModelFactoryTest
 *
 * @package Tests\Unit\Projects
 */
final class MetaDataDoctrineModelFactoryTest extends TestCase
{
    use ProjectHelper;
    use UsersHelper;

    //region Tests

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $project = $this->createProjectModel();
        $user = $this->createUserModel();
        $name = $this->getFaker()->word;
        $value = $this->getFaker()->word;

        $this->assertEquals(
            new MetaDataDoctrineModel($project, $user, $name, $value),
            $this->getMetaDataDoctrineModelFactory()->create($project, $user, $name, $value)
        );
    }

    //endregion

    /**
     * @return MetaDataDoctrineModelFactory
     */
    private function getMetaDataDoctrineModelFactory(): MetaDataDoctrineModelFactory
    {
        return new MetaDataDoctrineModelFactory();
    }
}
