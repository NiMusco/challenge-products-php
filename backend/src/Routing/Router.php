<?php

declare(strict_types=1);

namespace App\Routing;

use App\Controllers\ProductController;
use App\Exceptions\HttpException;
use App\Http\JsonResponse;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Throwable;

final class Router
{
    public function __construct(
        private readonly ProductController $products,
    ) {
    }

    public function dispatch(string $httpMethod, string $uri): void
    {
        try {
            $this->dispatchRoute($httpMethod, $uri);
        } catch (HttpException $exception) {
            $payload = [
                'error' => $exception->getMessage(),
                'code' => $exception->errorCode(),
            ];

            if ($exception->details() !== null) {
                $payload['details'] = $exception->details();
            }

            JsonResponse::send($payload, $exception->status());
        } catch (Throwable) {
            JsonResponse::send([
                'error' => 'Internal server error.',
                'code' => 'SERVER_ERROR',
            ], 500);
        }
    }

    private function dispatchRoute(string $httpMethod, string $uri): void
    {
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r): void {
            $r->addRoute('GET', '/productos', 'list');
            $r->addRoute('GET', '/productos/{id:\d+}', 'get');
            $r->addRoute('POST', '/productos', 'create');
            $r->addRoute('PUT', '/productos/{id:\d+}', 'update');
            $r->addRoute('DELETE', '/productos/{id:\d+}', 'delete');
        });

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new HttpException('Route not found.', 404, 'NOT_FOUND');

            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new HttpException('Method not allowed.', 405, 'METHOD_NOT_ALLOWED');

            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                match ($handler) {
                    'list' => $this->products->list(),
                    'get' => $this->products->get((int) $vars['id']),
                    'create' => $this->products->create(),
                    'update' => $this->products->update((int) $vars['id']),
                    'delete' => $this->products->delete((int) $vars['id']),
                    default => throw new HttpException('Unhandled route.', 404, 'NOT_FOUND'),
                };
                break;
        }
    }
}
