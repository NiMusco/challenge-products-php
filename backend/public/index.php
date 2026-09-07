<?php

declare(strict_types=1);

use App\Controllers\ProductController;
use App\Repositories\ProductRepository;
use App\Routing\Router;
use App\Utils\PriceConverter;
use Dotenv\Dotenv;

use function App\Utils\env;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Expose-Headers: X-Page, X-Per-Page, X-Total-Count, X-Total-Pages');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$priceConverter = new PriceConverter((float) env('PRECIO_USD', 0));
$controller = new ProductController(
    new ProductRepository(),
    $priceConverter,
);
$router = new Router($controller);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/'
);
