<?php

declare(strict_types=1);

namespace App\Exceptions;

final class ValidationException extends HttpException
{
    /** @param array<string, string> $details */
    public function __construct(
        string $message,
        array $details = [],
    ) {
        parent::__construct($message, 422, 'VALIDATION_ERROR', $details);
    }
}
