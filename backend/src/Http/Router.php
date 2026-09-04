<?php

declare(strict_types=1);

namespace App\Http;

use App\Controllers\ProductController;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

final class Router
{
    public function __construct(
        private readonly ProductController $products,
    ) {
    }

    public function dispatch(string $httpMethod, string $uri): void
    {
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r): void {
            $r->addRoute('GET', '/productos', 'index');
            $r->addRoute('GET', '/productos/{id:\d+}', 'show');
            $r->addRoute('POST', '/productos', 'store');
            $r->addRoute('PUT', '/productos/{id:\d+}', 'update');
            $r->addRoute('DELETE', '/productos/{id:\d+}', 'destroy');
        });

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                JsonResponse::error('Route not found.', 404, 'NOT_FOUND');
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                JsonResponse::error('Method not allowed.', 405, 'METHOD_NOT_ALLOWED');
                break;

            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                match ($handler) {
                    'index' => $this->products->index(),
                    'show' => $this->products->show((int) $vars['id']),
                    'store' => $this->products->store(),
                    'update' => $this->products->update((int) $vars['id']),
                    'destroy' => $this->products->destroy((int) $vars['id']),
                    default => JsonResponse::error('Unhandled route.', 500, 'SERVER_ERROR'),
                };
                break;
        }
    }
}
