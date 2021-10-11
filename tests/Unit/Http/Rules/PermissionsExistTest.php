<?php

namespace Tests\Unit\Http\Rules;

use App\Http\Rules\PermissionsExist;
use App\Models\Exceptions\ModelsNotFoundException;
use App\Projects\RoleManager;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\Helper\ProjectHelper;
use Tests\Helper\ReflectionHelper;
use Tests\TestCase;

final class PermissionsExistTest extends TestCase
{
    use ProjectHelper;
    use ReflectionHelper;

    private function getPermissionsExistRule(RoleManager $roleManager = null): PermissionsExist
    {
        return new PermissionsExist($roleManager ?: $this->createRoleManager());
    }

    public function testMessage(): void
    {
        $identifier = $this->getFaker()->word;
        $rule = $this->getPermissionsExistRule();
        $this->setPrivateProperty($rule, 'notFoundIdentifiers', [$identifier]);

        $this->assertEquals(\sprintf('validation.permissions-not-found:%s', $identifier), $rule->message());
    }

    public function testMessageWithoutNotFoundIdentifiers(): void
    {
        $this->assertEquals('validation.permissions-not-found:', $this->getPermissionsExistRule()->message());
    }

    private function setUpPassesTest(bool $withPermissions = true): array
    {
        $permissionName = $this->getFaker()->word;
        $permission = $this->createPermissionModel();
        $roleManager = $this->createRoleManager();
        $this->mockRoleMangerGetPermissions(
            $roleManager,
            $withPermissions ? new ArrayCollection([$permission]) : new ModelsNotFoundException('PermissionModel', [$permissionName]),
            [$permissionName]
        );
        $rule = $this->getPermissionsExistRule($roleManager);

        return [$rule, [$permissionName], $permission];
    }

    public function testPasses(): void
    {
        /** @var PermissionsExist $rule */
        [$rule, $permissionNames, $permission] = $this->setUpPassesTest();

        $this->assertTrue($rule->passes($this->getFaker()->word, $permissionNames));
        $this->assertEquals(new ArrayCollection([$permission]), $rule->getPermissions());
    }

    public function testPassesWithoutPermissionsFound(): void
    {
        /** @var PermissionsExist $rule */
        [$rule, $permissionNames] = $this->setUpPassesTest(false);

        $this->assertFalse($rule->passes($this->getFaker()->word, $permissionNames));
        $this->assertEquals($permissionNames, $this->getPrivateProperty($rule, 'notFoundIdentifiers'));
    }

    public function testPassesWithoutArrayValue(): void
    {
        $rule = $this->getPermissionsExistRule();

        $this->assertTrue($rule->passes($this->getFaker()->word, $this->getFaker()->word));
        $this->assertEquals(new ArrayCollection([]), $rule->getPermissions());
    }
}
