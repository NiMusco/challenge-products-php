<?php

declare(strict_types=1);

namespace Tests;

use PDO;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private static ?PDO $testConnection = null;

    protected function apiBaseUrl(): string
    {
        return rtrim(getenv('API_BASE_URL') ?: 'http://127.0.0.1:8091', '/');
    }

    protected function refreshDatabase(): void
    {
        $pdo = $this->testConnection();
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        $pdo->exec('TRUNCATE TABLE productos');
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function testConnection(): PDO
    {
        if (self::$testConnection instanceof PDO) {
            return self::$testConnection;
        }

        $host = getenv('DB_TEST_HOST') ?: (getenv('DB_HOST') ?: 'db_test');
        $port = getenv('DB_TEST_PORT') ?: (getenv('DB_PORT') ?: '3306');
        $database = getenv('DB_TEST_DATABASE') ?: (getenv('DB_DATABASE') ?: 'productos');
        $username = getenv('DB_USERNAME') ?: 'productos';
        $password = getenv('DB_PASSWORD') ?: 'secret';

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);

        self::$testConnection = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return self::$testConnection;
    }

    /**
     * @param array<string, mixed>|null $body
     * @return array{status: int, body: mixed, raw: string}
     */
    protected function api(string $method, string $path, ?array $body = null): array
    {
        $url = $this->apiBaseUrl() . $path;
        $headers = [
            'Accept: application/json',
        ];
        $payload = null;

        if ($body !== null) {
            $headers[] = 'Content-Type: application/json';
            $payload = json_encode($body, JSON_UNESCAPED_UNICODE);
        }

        $context = stream_context_create([
            'http' => [
                'method' => strtoupper($method),
                'header' => implode("\r\n", $headers),
                'content' => $payload ?? '',
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
