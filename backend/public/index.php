<?php

declare(strict_types=1);

use App\Controllers\ProductController;
use App\Http\Router;
use App\Presenters\ProductPresenter;
use App\Repositories\ProductRepository;
use App\Services\PriceConverter;

require dirname(__DIR__) . '/bootstrap.php';

$priceConverter = new PriceConverter((float) ($_ENV['PRECIO_USD'] ?? 0));
$presenter = new ProductPresenter($priceConverter);
$controller = new ProductController(new ProductRepository(), $presenter);
$router = new Router($controller);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/'
);
