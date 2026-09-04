<?php

declare(strict_types=1);

namespace App\Http;

use App\Exceptions\HttpException;

/** Predictable API error payload ({ error, code, details? }). */
final class ApiError
{
    /**
     * @param array<string, string>|null $details
     */
    public function __construct(
        private readonly string $error,
        private readonly string $code,
        private readonly ?array $details = null,
    ) {
    }

    public static function fromHttpException(HttpException $exception): self
    {
        return new self(
            $exception->getMessage(),
            $exception->errorCode(),
            $exception->details(),
        );
    }

    public static function serverError(string $message = 'Internal server error.'): self
    {
        return new self($message, 'SERVER_ERROR');
    }

    /** @return array{error: string, code: string, details?: array<string, string>} */
    public function toArray(): array
    {
        $payload = [
            'error' => $this->error,
            'code' => $this->code,
        ];

        if ($this->details !== null) {
            $payload['details'] = $this->details;
        }

        return $payload;
    }
}
