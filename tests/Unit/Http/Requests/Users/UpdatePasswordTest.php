<?php

namespace Tests\Unit\Http\Requests\Users;

use App\Http\Requests\Users\UpdatePassword;
use Tests\TestCase;

/**
 * Class UpdatePasswordTest
 *
 * @package Tests\Unit\Http\Requests\Users
 */
final class UpdatePasswordTest extends TestCase
{
    //region Tests

    /**
     * @return void
     */
    public function testRules(): void
    {
        $this->assertEquals(
            ['password' => ['string']],
            $this->getUpdatePassword()->rules()
        );
    }

    /**
     * @return void
     */
    public function testGetUserPassword(): void
    {
        $password = $this->getFaker()->password;
        $request = $this->getUpdatePassword();
        $request->offsetSet('password', $password);

        $this->assertEquals($password, $request->getUserPassword());
    }

    /**
     * @return void
     */
    public function testGetUserPasswordWithoutPassword(): void
    {
        $this->assertNull($this->getUpdatePassword()->getUserPassword());
    }

    //endregion

    /**
     * @return UpdatePassword
     */
    private function getUpdatePassword(): UpdatePassword
    {
        return new UpdatePassword();
    }
}
