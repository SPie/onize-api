<?php

namespace Tests\Unit\Http\Binders;

use App\Http\Binders\RoleBinder;
use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\RoleManager;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class RoleBinderTest
 *
 * @package Tests\Unit\Http\Binders
 */
final class RoleBinderTest extends TestCase
{
    use ProjectHelper;

    //region Tests

    /**
     * @return array
     */
    private function setUpBindTest(bool $withRole = true): array
    {
        $identifier = $this->getFaker()->word;
        $role = $this->createRoleModel();
        $roleManager = $this->createRoleManager();
        $this->mockRoleManagerGetRole($roleManager, $withRole ? $role : new ModelNotFoundException(), $identifier);
        $roleBinder = $this->getRoleBinder($roleManager);

        return [$roleBinder, $identifier, $role];
    }

    /**
     * @return void
     */
    public function testBind(): void
    {
        /** @var RoleBinder $roleBinder */
        [$roleBinder, $identifier, $role] = $this->setUpBindTest();

        $this->assertEquals($role, $roleBinder->bind($identifier));
    }

    /**
     * @return void
     */
    public function testBindWithoutRole(): void
    {
        /** @var RoleBinder $roleBinder */
        [$roleBinder, $identifier] = $this->setUpBindTest(false);

        $this->expectException(ModelNotFoundException::class);

        $roleBinder->bind($identifier);
    }

    //endregion

    /**
     * @param RoleManager|null $roleManager
     *
     * @return RoleBinder
     */
    private function getRoleBinder(RoleManager $roleManager = null): RoleBinder
    {
        return new RoleBinder($roleManager ?: $this->createRoleManager());
    }
}
