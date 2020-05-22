<?php

namespace Tests\Helper;

use App\Http\Requests\Users\Register;
use Mockery as m;

/**
 * Trait HttpHelper
 *
 * @package Tests\Helper
 */
trait HttpHelper
{
    /**
     * @param string|null $email
     * @param string|null $password
     *
     * @return Register
     */
    private function createRegister(string $email = null, string $password = null): Register
    {
        $request = m::spy(Register::class);
        $request
            ->shouldReceive('getEmail')
            ->andReturn($email ?: $this->getFaker()->safeEmail)
            ->getMock()
            ->shouldReceive('getPassword')
            ->andReturn($password ?: $this->getFaker()->password);

        return $request;
    }
}
