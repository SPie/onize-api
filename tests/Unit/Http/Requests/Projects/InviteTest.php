<?php

namespace Tests\Unit\Http\Requests\Projects;

use App\Http\Requests\Projects\Invite;
use App\Http\Rules\RoleExists;
use App\Http\Rules\ValidMetaData;
use Tests\Helper\HttpHelper;
use Tests\Helper\ProjectHelper;
use Tests\Helper\ReflectionHelper;
use Tests\TestCase;

final class InviteTest extends TestCase
{
    use HttpHelper;
    use ProjectHelper;
    use ReflectionHelper;

    private function getInvite(ValidMetaData $rule = null, RoleExists $roleExists = null): Invite
    {
        return new Invite(
            $rule ?: $this->createValidMetaDataRule(),
            $roleExists ?: $this->createRoleExistsRule()
        );
    }

    public function testRules(): void
    {
        $project = $this->createProjectModel();
        $roleExistsRule = $this->createRoleExistsRule();
        $rule = $this->createValidMetaDataRule();
        $route = $this->createRoute();
        $this->mockRouteParameter($route, $project, 'project', null);
        $request  = $this->getInvite($rule, $roleExistsRule);
        $request->setRouteResolver(fn () => $route);

        $this->assertEquals(
            [
                'role'     => ['required', $roleExistsRule],
                'email'    => ['required', 'email'],
                'metaData' => [$rule],
            ],
            $request->rules()
        );
        $this->assertValidMetaDataRuleSetProject($rule, $project);
        $this->assertRoleExistsRuleSetProject($roleExistsRule, $project);
    }

    public function testGetEmail(): void
    {
        $email = $this->getFaker()->safeEmail;
        $request = $this->getInvite();
        $request->offsetSet('email', $email);

        $this->assertEquals($email, $request->getEmail());
    }

    public function testGetMetaData(): void
    {
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $request = $this->getInvite();
        $request->offsetSet('metaData', $metaData);

        $this->assertEquals($metaData, $request->getMetaData());
    }

    public function testGetMetaDataWithoutMetaData(): void
    {
        $this->assertEquals([], $this->getInvite()->getMetaData());
    }

    public function testGetRole(): void
    {
        $role = $this->createRoleModel();
        $roleExistsRule = $this->createRoleExistsRule();
        $this->mockRoleExistsRuleGetRole($roleExistsRule, $role);

        $this->assertEquals($role, $this->getInvite(null, $roleExistsRule)->getRole());
    }
}
