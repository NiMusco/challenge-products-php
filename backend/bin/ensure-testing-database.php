#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Waits for the Docker db_test MySQL service and ensures the schema exists.
 */

$host = getenv('DB_TEST_HOST') ?: 'db_test';
$port = getenv('DB_TEST_PORT') ?: '3306';
$database = getenv('DB_TEST_DATABASE') ?: 'productos';
$username = getenv('DB_USERNAME') ?: 'productos';
$password = getenv('DB_PASSWORD') ?: 'secret';

$schema = <<<'SQL'
CREATE TABLE IF NOT EXISTS productos (
    id INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL;

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);
$attempts = 40;
$lastError = null;

for ($i = 1; $i <= $attempts; $i++) {
    try {
        $connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $connection->exec($schema);
        echo "Test MySQL \"{$host}/{$database}\" is ready." . PHP_EOL;
        exit(0);
    } catch (Throwable $exception) {
        $lastError = $exception->getMessage();
        usleep(250_000);
    }
}

fwrite(
    STDERR,
    "Failed to reach test database at {$host}:{$port}/{$database}." . PHP_EOL
    . "Start the stack so db_test is healthy: docker compose up -d db_test" . PHP_EOL
    . "Last error: {$lastError}" . PHP_EOL
);
exit(1);
