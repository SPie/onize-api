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

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $project = $this->createProjectModel();
        $label = $this->getFaker()->word;
        $owner = $this->getFaker()->boolean;
        $uuid = $this->getFaker()->uuid;
        $uuidGenerator = $this->createUuidGenerator($uuid);

        $this->assertEquals(
            new RoleDoctrineModel(
                $uuid,
                $project,
                $label,
                $owner
            ),
            $this->getRoleDoctrineModelFactory($uuidGenerator)->create($project, $label, $owner)
        );
    }

    //endregion

    /**
     * @param UuidGenerator|null $uuidGenerator
     *
     * @return RoleDoctrineModelFactory
     */
    private function getRoleDoctrineModelFactory(UuidGenerator $uuidGenerator = null): RoleDoctrineModelFactory
    {
        return new RoleDoctrineModelFactory($uuidGenerator ?: $this->createUuidGenerator());
    }
}
