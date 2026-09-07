<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

use function App\Utils\env;

abstract class ProductsTestCase extends BaseTestCase
{
    private static array $createdProductIds = [];

    protected function tearDown(): void
    {
        $this->cleanupCreatedProducts();
        parent::tearDown();
    }

    public function cleanupCreatedProducts(): void
    {
        foreach (array_unique(self::$createdProductIds) as $id) {
            $this->api('DELETE', '/productos/' . $id);
        }

        self::$createdProductIds = [];
    }

    protected function apiBaseUrl(): string
    {
        return rtrim((string) env('API_BASE_URL', 'http://127.0.0.1'), '/');
    }

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
        $responseHeaders = [];
        if (isset($http_response_header) && is_array($http_response_header)) {
            if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches)) {
                $status = (int) $matches[1];
            }

            foreach ($http_response_header as $headerLine) {
                if (!str_contains($headerLine, ':')) {
                    continue;
                }

                [$name, $value] = explode(':', $headerLine, 2);
                $responseHeaders[strtolower(trim($name))] = trim($value);
            }
        }

        $decoded = null;
        if ($raw !== '') {
            $decoded = json_decode($raw, true);
        }

        if (
            strtoupper($method) === 'POST'
            && $path === '/productos'
            && $status === 201
            && isset($decoded['id'])
            && is_numeric($decoded['id'])
        ) {
            self::$createdProductIds[] = (int) $decoded['id'];
        }

        return [
            'status' => $status,
            'body' => $decoded,
            'headers' => $responseHeaders,
            'raw' => $raw,
        ];
    }

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
