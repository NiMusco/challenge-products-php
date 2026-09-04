<?php

declare(strict_types=1);

namespace App\Exceptions;

use InvalidArgumentException;

final class ValidationException extends InvalidArgumentException
{
    /** @param array<string, string> $details */
    public function __construct(
        string $message,
        private readonly array $details = [],
    ) {
        parent::__construct($message);
    }

    /** @return array<string, string> */
    public function details(): array
    {
        return $this->details;
    }
}
