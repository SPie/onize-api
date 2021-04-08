<?php

namespace App\Projects\Invites\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class AlreadyMemberException
 *
 * @package App\Projects\Invites\Exceptions
 */
final class AlreadyMemberException extends HttpException
{
    /**
     * AlreadyMemberException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct(400, $message);
    }
}
