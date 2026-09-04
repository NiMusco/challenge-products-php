<?php

declare(strict_types=1);

namespace App\Http;

final class JsonResponse
{
    public static function send(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function noContent(): void
    {
        http_response_code(204);
    }

    /**
     * @param array<string, mixed>|list<mixed>|null $details
     */
    public static function error(
        string $message,
        int $status = 400,
        string $code = 'ERROR',
        array|null $details = null,
    ): void {
        $payload = [
            'error' => $message,
            'code' => $code,
        ];

        if ($details !== null) {
            $payload['details'] = $details;
        }

        self::send($payload, $status);
    }
}
