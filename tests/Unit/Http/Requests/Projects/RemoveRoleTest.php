<?php

namespace Tests\Unit\Http\Requests\Projects;

use App\Http\Requests\Projects\RemoveRole;
use App\Http\Rules\RoleExists;
use Tests\Helper\HttpHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

final class RemoveRoleTest extends TestCase
{
    use HttpHelper;
    use ProjectHelper;

    private function getRemoveRole(RoleExists $roleExistsRule = null): RemoveRole
    {
        return new RemoveRole($roleExistsRule ?: $this->createRoleExistsRule());
    }

    public function testRules(): void
    {
        $project = $this->createProjectModel();
        $currentRole = $this->createRoleModel();
        $this->mockRoleModelGetProject($currentRole, $project);
        $roleExistsRule = $this->createRoleExistsRule();
        $route = $this->createRoute();
        $this->mockRouteParameter($route, $currentRole, 'role', null);
        $request = $this->getRemoveRole($roleExistsRule);
        $request->setRouteResolver(fn () => $route);

        $this->assertEquals(['newRole' => [$roleExistsRule]], $request->rules());
        $this->assertRoleExistsRuleSetProject($roleExistsRule, $project);
    }

    public function testGetNewRole(): void
    {
        $role = $this->createRoleModel();
        $roleExistsRule = $this->createRoleExistsRule();
        $this->mockRoleExistsRuleGetRole($roleExistsRule, $role);

        $this->assertEquals($role, $this->getRemoveRole($roleExistsRule)->getNewRole());
    }

    public function testGetNewRoleWithoutRole(): void
    {
        $roleExistsRule = $this->createRoleExistsRule();
        $this->mockRoleExistsRuleGetRole($roleExistsRule, null);

        $this->assertNull($this->getRemoveRole($roleExistsRule)->getNewRole());
    }
}
