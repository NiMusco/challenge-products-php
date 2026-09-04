<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Http\JsonResponse;
use App\Validators\ValidateProductBody;
use App\Presenters\ProductPresenter;
use App\Repositories\ProductRepository;
use Throwable;

final class ProductController
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_PER_PAGE = 5;

    public function __construct(
        private readonly ProductRepository $products,
        private readonly ProductPresenter $presenter,
        private readonly ValidateProductBody $validateProductBody = new ValidateProductBody(),
    ) {
    }

    public function index(): void
    {
        $page = max(1, $this->queryInt('page', self::DEFAULT_PAGE));
        $perPage = max(1, min(100, $this->queryInt('per_page', self::DEFAULT_PER_PAGE)));

        $result = $this->products->findPaginated($page, $perPage);
        $totalPages = $result['total'] > 0
            ? (int) ceil($result['total'] / $perPage)
            : 0;

        JsonResponse::send([
            'data' => $this->presenter->collection($result['items']),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $result['total'],
                'total_pages' => $totalPages,
            ],
        ]);
    }

    public function show(int $id): void
    {
        $product = $this->products->findById($id);

        if ($product === null) {
            JsonResponse::error('Product not found.', 404, 'NOT_FOUND');
            return;
        }

        JsonResponse::send($this->presenter->present($product));
    }

    public function store(): void
    {
        try {
            $request = $this->validateProductBody->validate();
            $id = $this->products->create($request->toArray());
            $product = $this->products->findById($id);

            JsonResponse::send($this->presenter->present($product ?? []), 201);
        } catch (ValidationException $exception) {
            JsonResponse::error(
                $exception->getMessage(),
                422,
                'VALIDATION_ERROR',
                $exception->details()
            );
        } catch (Throwable) {
            JsonResponse::error('Unable to create product.', 500, 'SERVER_ERROR');
        }
    }

    public function update(int $id): void
    {
        try {
            $request = $this->validateProductBody->validate();

            // Single write, then one read — avoid a pre-update existence SELECT.
            $this->products->update($id, $request->toArray());
            $product = $this->products->findById($id);

            if ($product === null) {
                JsonResponse::error('Product not found.', 404, 'NOT_FOUND');
                return;
            }

            JsonResponse::send($this->presenter->present($product));
        } catch (ValidationException $exception) {
            JsonResponse::error(
                $exception->getMessage(),
                422,
                'VALIDATION_ERROR',
                $exception->details()
            );
        } catch (Throwable) {
            JsonResponse::error('Unable to update product.', 500, 'SERVER_ERROR');
        }
    }

    public function destroy(int $id): void
    {
        if (!$this->products->delete($id)) {
            JsonResponse::error('Product not found.', 404, 'NOT_FOUND');
            return;
        }

        JsonResponse::noContent();
    }

    private function queryInt(string $key, int $default): int
    {
        $value = $_GET[$key] ?? null;

        if ($value === null || $value === '' || !is_numeric($value)) {
            return $default;
        }

        return (int) $value;
    }
}
