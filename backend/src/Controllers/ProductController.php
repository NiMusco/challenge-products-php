<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Http\JsonResponse;
use App\Repositories\ProductRepository;
use App\Services\PriceConverter;
use Throwable;

final class ProductController
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_PER_PAGE = 10;

    public function __construct(
        private readonly ProductRepository $products,
        private readonly PriceConverter $priceConverter,
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

        $items = array_map(
            fn (array $product): array => $this->present($product),
            $result['items']
        );

        JsonResponse::send([
            'data' => $items,
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

        JsonResponse::send($this->present($product));
    }

    public function store(): void
    {
        try {
            $payload = $this->validatedPayload($this->readJsonBody());
            $id = $this->products->create($payload);
            $product = $this->products->findById($id);

            JsonResponse::send($this->present($product ?? []), 201);
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
            $payload = $this->validatedPayload($this->readJsonBody());

            // Single write, then one read — avoid a pre-update existence SELECT.
            // Unchanged values still return the product via findById.
            $this->products->update($id, $payload);
            $product = $this->products->findById($id);

            if ($product === null) {
                JsonResponse::error('Product not found.', 404, 'NOT_FOUND');
                return;
            }

            JsonResponse::send($this->present($product));
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

    /** @param array<string, mixed> $product */
    private function present(array $product): array
    {
        $precio = (float) ($product['precio'] ?? 0);

        return [
            'id' => (int) ($product['id'] ?? 0),
            'nombre' => (string) ($product['nombre'] ?? ''),
            'descripcion' => (string) ($product['descripcion'] ?? ''),
            'precio' => $precio,
            'precio_usd' => $this->priceConverter->toUsd($precio),
            'created_at' => $product['created_at'] ?? null,
            'updated_at' => $product['updated_at'] ?? null,
        ];
    }

    /** @return array<string, mixed> */
    private function readJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw === false ? '' : $raw, true);

        if (!is_array($decoded)) {
            throw new ValidationException('Invalid JSON body.', [
                'body' => 'Request body must be a valid JSON object.',
            ]);
        }

        return $decoded;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{nombre: string, descripcion: string, precio: float}
     */
    private function validatedPayload(array $payload): array
    {
        $details = [];

        $nombre = trim((string) ($payload['nombre'] ?? ''));
        $descripcion = trim((string) ($payload['descripcion'] ?? ''));
        $precio = $payload['precio'] ?? null;

        if ($nombre === '') {
            $details['nombre'] = 'Field "nombre" is required.';
        }

        if ($descripcion === '') {
            $details['descripcion'] = 'Field "descripcion" is required.';
        }

        if (!is_numeric($precio) || (float) $precio < 0) {
            $details['precio'] = 'Field "precio" must be a non-negative number.';
        }

        if ($details !== []) {
            throw new ValidationException('Validation failed.', $details);
        }

        return [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => (float) $precio,
        ];
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
