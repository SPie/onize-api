<?php

namespace Tests\Unit\Http\Requests\Users;

use App\Http\Requests\Users\Register;
use App\Http\Rules\UniqueUser;
use Tests\Helper\HttpHelper;
use Tests\TestCase;

final class RegisterTest extends TestCase
{
    use HttpHelper;

    private function getRegister(UniqueUser $uniqueUser = null): Register
    {
        return new Register($uniqueUser ?: $this->createUniqueUser());
    }

    public function testRules(): void
    {
        $uniqueUser = $this->createUniqueUser();

        $this->assertEquals(
            [
                'email'    => ['required', 'email', $uniqueUser],
                'password' => ['required', 'string'],
            ],
            $this->getRegister($uniqueUser)->rules()
        );
    }

    public function testGetEmail(): void
    {
        $email = $this->getFaker()->safeEmail;
        $request = $this->getRegister();
        $request->offsetSet('email', $email);

        $this->assertEquals($email, $request->getEmail());
    }

    public function testGetPassword(): void
    {
        $password = $this->getFaker()->password;
        $request = $this->getRegister();
        $request->offsetSet('password', $password);

        $this->assertEquals($password, $request->getPassword());
    }
}
