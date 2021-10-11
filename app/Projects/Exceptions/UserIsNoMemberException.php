<?php

namespace App\Projects\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class UserIsNoMemberException extends HttpException
{
    public function __construct(string $message = '')
    {
        parent::__construct(400, $message);
    }
}
