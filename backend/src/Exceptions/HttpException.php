<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

abstract class HttpException extends RuntimeException
{
    /**
     * @param array<string, string>|null $details
     */
    public function __construct(
        string $message,
        private readonly int $status,
        private readonly string $errorCode,
        private readonly ?array $details = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function status(): int
    {
        return $this->status;
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }

    /** @return array<string, string>|null */
    public function details(): ?array
    {
        return $this->details;
    }
}
