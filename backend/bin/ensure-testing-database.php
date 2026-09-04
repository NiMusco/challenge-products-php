#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Waits for the app MySQL (DB_HOST) before Pest runs.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

$host = (string) env('DB_HOST', 'db');
$port = (string) env('DB_PORT', '3306');
$database = (string) env('DB_DATABASE', 'productos');
$username = (string) env('DB_USERNAME', 'productos');
$password = (string) env('DB_PASSWORD', 'secret');

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);
$attempts = 40;
$lastError = null;

for ($i = 1; $i <= $attempts; $i++) {
    try {
        $connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $connection->query('SELECT 1 FROM productos LIMIT 1');
        echo "MySQL \"{$host}/{$database}\" is ready." . PHP_EOL;
        exit(0);
    } catch (Throwable $exception) {
        $lastError = $exception->getMessage();
        usleep(250_000);
    }
}

fwrite(
    STDERR,
    "Failed to reach database at {$host}:{$port}/{$database}." . PHP_EOL
    . "Start the stack: docker compose up -d" . PHP_EOL
    . "Last error: {$lastError}" . PHP_EOL
);
exit(1);
