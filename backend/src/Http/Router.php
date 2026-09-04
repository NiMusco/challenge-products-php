<?php

declare(strict_types=1);

namespace App\Http;

use App\Controllers\ProductController;
use App\Exceptions\MethodNotAllowedException;
use App\Exceptions\NotFoundException;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Throwable;

final class Router
{
    public function __construct(
        private readonly ProductController $products,
        private readonly ExceptionHandler $exceptions = new ExceptionHandler(),
    ) {
    }

    public function dispatch(string $httpMethod, string $uri): void
    {
        try {
            $this->dispatchRoute($httpMethod, $uri);
        } catch (Throwable $exception) {
            $this->exceptions->handle($exception);
        }
    }

    private function dispatchRoute(string $httpMethod, string $uri): void
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
                throw new NotFoundException('Route not found.');

            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedException();

            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                match ($handler) {
                    'index' => $this->products->index(),
                    'show' => $this->products->show((int) $vars['id']),
                    'store' => $this->products->store(),
                    'update' => $this->products->update((int) $vars['id']),
                    'destroy' => $this->products->destroy((int) $vars['id']),
                    default => throw new NotFoundException('Unhandled route.'),
                };
                break;
        }
    }
}
