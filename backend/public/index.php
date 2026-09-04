<?php

declare(strict_types=1);

use App\Controllers\ProductController;
use App\Http\JsonResponse;
use App\Repositories\ProductRepository;
use App\Services\PriceConverter;
use Dotenv\Dotenv;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

// Prefer process environment (e.g. Docker Compose) over .env file values
foreach (['PRECIO_USD', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'] as $key) {
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$controller = new ProductController(
    new ProductRepository(),
    new PriceConverter()
);

$dispatcher = FastRoute\simpleDispatcher(static function (RouteCollector $r): void {
    $r->addRoute('GET', '/productos', 'index');
    $r->addRoute('GET', '/productos/{id:\d+}', 'show');
    $r->addRoute('POST', '/productos', 'store');
    $r->addRoute('PUT', '/productos/{id:\d+}', 'update');
    $r->addRoute('DELETE', '/productos/{id:\d+}', 'destroy');
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'] ?? '/';

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        JsonResponse::error('Route not found.', 404);
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        JsonResponse::error('Method not allowed.', 405);
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        match ($handler) {
            'index' => $controller->index(),
            'show' => $controller->show((int) $vars['id']),
            'store' => $controller->store(),
            'update' => $controller->update((int) $vars['id']),
            'destroy' => $controller->destroy((int) $vars['id']),
            default => JsonResponse::error('Unhandled route.', 500),
        };
        break;
}
