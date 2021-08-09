<?php

namespace Tests\Unit\Projects;

use App\Models\UuidGenerator;
use App\Projects\RoleDoctrineModel;
use App\Projects\RoleDoctrineModelFactory;
use Tests\Helper\ModelHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class RoleDoctrineModelFactoryTest
 *
 * @package Tests\Unit\Projects
 */
final class RoleDoctrineModelFactoryTest extends TestCase
{
    use ModelHelper;
    use ProjectHelper;

    //region Tests

    public function testCreate(): void
    {
        $project = $this->createProjectModel();
        $label = $this->getFaker()->word;
        $owner = $this->getFaker()->boolean;

        $this->assertEquals(
            new RoleDoctrineModel(
                $project,
                $label,
                $owner
            ),
            $this->getRoleDoctrineModelFactory()->create($project, $label, $owner)
        );
    }

    //endregion

    private function getRoleDoctrineModelFactory(): RoleDoctrineModelFactory
    {
        return new RoleDoctrineModelFactory();
    }
}
