<?php

namespace Tests\Unit\Http\Requests\Auth;

use App\Http\Requests\Auth\Authenticate;
use Tests\TestCase;

/**
 * Class AuthenticateTest
 *
 * @package Tests\Unit\Http\Requests\Auth
 */
final class AuthenticateTest extends TestCase
{
    //region Tests

    /**
     * @return void
     */
    public function testRules(): void
    {
        $this->assertEquals(
            [
                'email'    => ['required'],
                'password' => ['required'],
            ],
            $this->getAuthenticate()->rules()
        );
    }

    /**
     * @return void
     */
    public function testGetEmail(): void
    {
        $email = $this->getFaker()->safeEmail;
        $request = $this->getAuthenticate();
        $request->offsetSet('email', $email);

        $this->assertEquals($email, $request->getEmail());
    }

    /**
     * @return void
     */
    public function testGetPassword(): void
    {
        $password = $this->getFaker()->password;
        $request = $this->getAuthenticate();
        $request->offsetSet('password', $password);

        $this->assertEquals($password, $request->getPassword());
    }

    //endregion

    /**
     * @return Authenticate
     */
    private function getAuthenticate(): Authenticate
    {
        return new Authenticate();
    }
}
