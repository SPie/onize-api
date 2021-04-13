<?php

namespace Tests\Unit\Http\Requests\Projects;

use App\Http\Requests\Projects\Invite;
use App\Http\Rules\RoleExists;
use Tests\Helper\HttpHelper;
use Tests\Helper\ProjectHelper;
use Tests\TestCase;

/**
 * Class InviteTest
 *
 * @package Tests\Unit\Http\Requests\Projects
 */
final class InviteTest extends TestCase
{
    use HttpHelper;
    use ProjectHelper;

    //region Tests

    /**
     * @return void
     */
    public function testRules(): void
    {
        $roleExists = $this->createRoleExistsRule();

        $this->assertEquals(
            [
                'role'     => ['required', $roleExists],
                'email'    => ['required', 'email'],
                'metaData' => ['array'],
            ],
            $this->getInvite()->rules()
        );
    }

    /**
     * @return void
     */
    public function testGetRole(): void
    {
        $role = $this->createRoleModel();
        $roleExists = $this->createRoleExistsRule();
        $this->mockRoleExistsRuleGetRole($roleExists, $role);

        $this->assertEquals($role, $this->getInvite($roleExists)->getRole());
    }

    /**
     * @return void
     */
    public function testGetEmail(): void
    {
        $email = $this->getFaker()->safeEmail;
        $request = $this->getInvite();
        $request->offsetSet('email', $email);

        $this->assertEquals($email, $request->getEmail());
    }

    /**
     * @return void
     */
    public function testGetMetaData(): void
    {
        $metaData = [$this->getFaker()->word => $this->getFaker()->word];
        $request = $this->getInvite();
        $request->offsetSet('metaData', $metaData);

        $this->assertEquals($metaData, $request->getMetaData());
    }

    /**
     * @return void
     */
    public function testGetMetaDataWithoutMetaData(): void
    {
        $this->assertEquals([], $this->getInvite()->getMetaData());
    }

    //endregion

    /**
     * @return Invite
     */
    private function getInvite(RoleExists $roleExists = null): Invite
    {
        return new Invite($roleExists ?: $this->createRoleExistsRule());
    }
}
