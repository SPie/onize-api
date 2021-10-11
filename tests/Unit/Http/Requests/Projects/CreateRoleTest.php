<?php

namespace Tests\Unit\Http\Requests\Projects;

use App\Http\Requests\Projects\CreateRole;
use App\Http\Rules\PermissionsExist;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\Helper\HttpHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

final class CreateRoleTest extends TestCase
{
    use HttpHelper;
    use ProjectHelper;

    private function getCreateRoleRequest(PermissionsExist $permissionsExistRule = null): CreateRole
    {
        return new CreateRole(
            $permissionsExistRule ?: $this->createPermissionsExistRule()
        );
    }

    public function testRules(): void
    {
        $permissionsExistRule = $this->createPermissionsExistRule();

        $this->assertEquals(
            [
                'label'         => ['required', 'string'],
                'permissions'   => ['array', $permissionsExistRule],
                'permissions.*' => ['distinct'],
            ],
            $this->getCreateRoleRequest($permissionsExistRule)->rules()
        );
    }

    public function testGetLabel(): void
    {
        $label = $this->getFaker()->word;
        $request = $this->getCreateRoleRequest();
        $request->offsetSet('label', $label);

        $this->assertEquals($label, $request->getLabel());
    }

    public function testGetPermissions(): void
    {
        $permission = $this->createPermissionModel();
        $rule = $this->createPermissionsExistRule(new ArrayCollection([$permission]));

        $this->assertEquals(
            new ArrayCollection([$permission]),
            $this->getCreateRoleRequest($rule)->getPermissions()
        );
    }
}
