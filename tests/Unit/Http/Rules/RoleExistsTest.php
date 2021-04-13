<?php

namespace Tests\Unit\Http\Rules;

use App\Http\Rules\RoleExists;
use App\Models\Exceptions\ModelNotFoundException;
use App\Projects\RoleManager;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class RoleExistsTest
 *
 * @package Tests\Unit\Http\Rules
 */
final class RoleExistsTest extends TestCase
{
    use ProjectHelper;

    //region Tests

    /**
     * @return array
     */
    private function setUpPassesTest(bool $withRole = true): array
    {
        $uuid = $this->getFaker()->uuid;
        $role = $this->createRoleModel();
        $roleManager = $this->createRoleManager();
        $this->mockRoleManagerGetRole($roleManager, $withRole ? $role : new ModelNotFoundException(), $uuid);
        $rule = $this->getRoleExists($roleManager);

        return [$rule, $uuid, $role];
    }

    /**
     * @return void
     */
    public function testPassesWithRole(): void
    {
        /** @var RoleExists $rule */
        [$rule, $uuid, $role] = $this->setUpPassesTest();

        $this->assertTrue($rule->passes($this->getFaker()->word, $uuid));
        $this->assertEquals($role, $rule->getRole());
    }

    /**
     * @return void
     */
    public function testPassesWithoutRole(): void
    {
        /** @var RoleExists $rule */
        [$rule, $uuid] = $this->setUpPassesTest(false);

        $this->assertFalse($rule->passes($this->getFaker()->word, $uuid));
    }

    /**
     * @return void
     */
    public function testMessage(): void
    {
        $this->assertEquals('validation.role-not-found', $this->getRoleExists()->message());
    }

    //endregion

    /**
     * @return RoleExists
     */
    private function getRoleExists(RoleManager $roleManager = null): RoleExists
    {
        return new RoleExists($roleManager ?: $this->createRoleManager());
    }
}
