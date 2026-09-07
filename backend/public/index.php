<?php

declare(strict_types=1);

use App\Controllers\ProductController;
use App\Repositories\ProductRepository;
use App\Routing\Router;
use App\Utils\PriceConverter;

use function App\Utils\env;

require dirname(__DIR__) . '/bootstrap.php';

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
