<?php

namespace Tests\Unit\Http\Requests\Projects;

use App\Http\Requests\Projects\ChangeRole;
use App\Http\Rules\RoleExists;
use App\Http\Rules\UserExistsAndIsMember;
use Tests\Helper\HttpHelper;
use Tests\Helper\ProjectHelper;
use Tests\Helper\UsersHelper;
use Tests\TestCase;

final class ChangeRoleTest extends TestCase
{
    use HttpHelper;
    use ProjectHelper;
    use UsersHelper;

    private function getChangeRole(
        UserExistsAndIsMember $userExistsAndIsMemberRule = null,
        RoleExists $roleExistsRule = null
    ): ChangeRole {
        return new ChangeRole(
            $userExistsAndIsMemberRule ?: $this->createUserExistsAndIsMemberRule(),
            $roleExistsRule ?: $this->createRoleExistsRule()
        );
    }

    public function testRules(): void
    {
        $project = $this->createProjectModel();
        $roleExistsRule = $this->createRoleExistsRule();
        $userExistsAndIsMemberRule = $this->createUserExistsAndIsMemberRule();
        $route = $this->createRoute();
        $this->mockRouteParameter($route, $project, 'project', null);
        $request = $this->getChangeRole($userExistsAndIsMemberRule, $roleExistsRule);
        $request->setRouteResolver(fn () => $route);

        $this->assertEquals(
            [
                'user' => ['required', $userExistsAndIsMemberRule],
                'role' => ['required', $roleExistsRule]
            ],
            $request->rules()
        );
        $this->assertRoleExistsRuleSetProject($roleExistsRule, $project);
        $this->assertUserExistsAndIsMemberRuleSetProject($userExistsAndIsMemberRule, $project);
    }

    public function testGetUser(): void
    {
        $user = $this->createUserModel();
        $userExistsAndIsMemberRule = $this->createUserExistsAndIsMemberRule();
        $this->mockUserExistsAndIsMemberRuleGetUser($userExistsAndIsMemberRule, $user);

        $this->assertEquals($user, $this->getChangeRole($userExistsAndIsMemberRule)->getUserModel());
    }

    public function testGetRole(): void
    {
        $role = $this->createRoleModel();
        $roleExistsRule = $this->createRoleExistsRule();
        $this->mockRoleExistsRuleGetRole($roleExistsRule, $role);

        $this->assertEquals($role, $this->getChangeRole(null, $roleExistsRule)->getRole());
    }
}
