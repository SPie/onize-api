<?php

namespace Tests\Unit\Http\Requests\Users;

use App\Http\Requests\Users\Register;
use App\Http\Requests\Validators\UniqueUser;
use Tests\Helper\HttpHelper;
use Tests\TestCase;

/**
 * Class RegisterTest
 *
 * @package Tests\Unit\Http\Requests\Users
 */
final class RegisterTest extends TestCase
{
    use HttpHelper;

    //region Tests

    /**
     * @return void
     */
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
     * @param UniqueUser|null $uniqueUser
     *
     * @return Register
     */
    private function getRegister(UniqueUser $uniqueUser = null): Register
    {
        return new Register($uniqueUser ?: $this->createUniqueUser());
    }
}
