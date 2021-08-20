<?php

namespace Tests\Unit\Http\Requests\Projects;

use App\Http\Requests\Projects\AcceptInvitation;
use App\Http\Rules\ValidMetaData;
use Tests\Helper\HttpHelper;
use Tests\Helper\ProjectHelper;
use Tests\Helper\ReflectionHelper;
use Tests\TestCase;

final class AcceptInvitationTest extends TestCase
{
    use HttpHelper;
    use ProjectHelper;
    use ReflectionHelper;

    public function testRules(): void
    {
        $project = $this->createProjectModel();
        $role = $this->createRoleModel();
        $this->mockRoleModelGetProject($role, $project);
        $invitation = $this->createInvitationModel();
        $this->mockInvitationModelGetRole($invitation, $role);
        $route = $this->createRoute();
        $this->mockRouteParameter($route, $invitation, 'invitation', null);
        $validMetaDataRule = $this->createValidMetaDataRule();
        $this->mockValidMetaDataRuleSetProject($validMetaDataRule, $project);
        $request = $this->getAcceptInvitation($validMetaDataRule);
        $request->setRouteResolver(fn () => $route);

        $this->assertEquals(['metaData' => [$validMetaDataRule]], $request->rules());
    }

    public function testGetMetaData(): void
    {
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $request = $this->getAcceptInvitation();
        $request->offsetSet('metaData', $metaData);

        $this->assertEquals($metaData, $request->getMetaData());
    }

    public function testGetMetaDataWithoutMetaData(): void
    {
        $this->assertEquals([], $this->getAcceptInvitation()->getMetaData());
    }

    private function getAcceptInvitation(ValidMetaData $validMetaData = null): AcceptInvitation
    {
        return new AcceptInvitation($validMetaData ?: $this->createValidMetaDataRule());
    }
}
