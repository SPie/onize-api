<?php

namespace App\Projects\Invites\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class UserNotMemberException extends HttpException
{
    public function __construct(string $message)
    {
        parent::__construct(400, $message);
    }
}
