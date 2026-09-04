<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class ProductsTestCase extends BaseTestCase
{
    protected function apiBaseUrl(): string
    {
        return rtrim((string) env('API_BASE_URL', 'http://127.0.0.1'), '/');
    }

    /**
     * @param array<string, mixed>|string|null $body Array is JSON-encoded; string is sent as raw body.
     * @return array{status: int, body: mixed, raw: string}
     */
    protected function api(string $method, string $path, array|string|null $body = null): array
    {
        $url = $this->apiBaseUrl() . $path;
        $headers = [
            'Accept: application/json',
        ];
        $payload = '';

        if (is_array($body)) {
            $headers[] = 'Content-Type: application/json';
            $payload = json_encode($body, JSON_UNESCAPED_UNICODE) ?: '';
        } elseif (is_string($body)) {
            $headers[] = 'Content-Type: application/json';
            $payload = $body;
        }

        $context = stream_context_create([
            'http' => [
                'method' => strtoupper($method),
                'header' => implode("\r\n", $headers),
                'content' => $payload,
                'ignore_errors' => true,
                'timeout' => 10,
            ],
        ]);

        $raw = @file_get_contents($url, false, $context);

        if ($raw === false) {
            $this->fail('HTTP request failed for ' . strtoupper($method) . ' ' . $url);
        }

        $status = 0;
        if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches)) {
            $status = (int) $matches[1];
        }

        $decoded = null;
        if ($raw !== '') {
            $decoded = json_decode($raw, true);
        }

        return [
            'status' => $status,
            'body' => $decoded,
            'raw' => $raw,
        ];
    }

    /** @return array{nombre: string, descripcion: string, precio: float} */
    protected function productPayload(?string $suffix = null): array
    {
        $suffix ??= uniqid('t_', true);

        return [
            'nombre' => 'Test Product ' . $suffix,
            'descripcion' => 'Created by Pest ' . $suffix,
            'precio' => 15000.50,
        ];
    }
}
