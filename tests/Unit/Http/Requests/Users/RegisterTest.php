<?php

namespace Tests\Unit\Http\Requests\Users;

use App\Http\Requests\Users\Register;
use Tests\TestCase;

/**
 * Class RegisterTest
 *
 * @package Tests\Unit\Http\Requests\Users
 */
final class RegisterTest extends TestCase
{
    //region Tests

    /**
     * @return void
     */
    public function testRules(): void
    {
        $this->assertEquals(
            [
                'email'    => ['required', 'email'],
                'password' => ['required', 'string'],
            ],
            $this->getRegister()->rules()
        );
    }

    /**
     * @return void
     */
    public function testGetEmail(): void
    {
        $email = $this->getFaker()->safeEmail;
        $request = $this->getRegister();
        $request->offsetSet('email', $email);

        $this->assertEquals($email, $request->getEmail());
    }

    /**
     * @return void
     */
    public function testGetPassword(): void
    {
        $password = $this->getFaker()->password;
        $request = $this->getRegister();
        $request->offsetSet('password', $password);

        $this->assertEquals($password, $request->getPassword());
    }

    //endregion

    /**
     * @return Register
     */
    private function getRegister(): Register
    {
        return new Register();
    }
}
