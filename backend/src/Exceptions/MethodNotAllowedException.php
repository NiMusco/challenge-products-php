<?php

declare(strict_types=1);

namespace App\Exceptions;

final class MethodNotAllowedException extends HttpException
{
    public function __construct(string $message = 'Method not allowed.')
    {
        parent::__construct($message, 405, 'METHOD_NOT_ALLOWED');
    }
}
