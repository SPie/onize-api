<?php

namespace Tests\Unit\Http\Requests\Projects;

use App\Http\Requests\Projects\Invite;
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

    //region Tests

    public function testRules(): void
    {
        $project = $this->createProjectModel();
        $role = $this->createRoleModel();
        $this->mockRoleModelGetProject($role, $project);
        $rule = $this->createValidMetaDataRule();
        $this->mockValidMetaDataRuleSetProject($rule, $project);
        $route = $this->createRoute();
        $this->mockRouteParameter($route, $role, 'role', null);
        $request  = $this->getInvite($rule);
        $request->setRouteResolver(fn () => $route);

        $this->assertEquals(
            [
                'email'    => ['required', 'email'],
                'metaData' => [$rule],
            ],
            $request->rules()
        );
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

    //endregion

    private function getInvite(ValidMetaData $rule = null): Invite
    {
        return new Invite($rule ?: $this->createValidMetaDataRule());
    }
}
