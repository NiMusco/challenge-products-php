<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\JsonResponse;
use App\Repositories\ProductRepository;
use App\Services\PriceConverter;
use InvalidArgumentException;
use Throwable;

final class ProductController
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly PriceConverter $priceConverter,
    ) {
    }

    public function index(): void
    {
        $items = array_map(
            fn (array $product): array => $this->present($product),
            $this->products->findAll()
        );

        JsonResponse::send($items);
    }

    public function show(int $id): void
    {
        $product = $this->products->findById($id);

        if ($product === null) {
            JsonResponse::error('Product not found.', 404);
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
        } catch (InvalidArgumentException $exception) {
            JsonResponse::error($exception->getMessage(), 422);
        } catch (Throwable $exception) {
            JsonResponse::error('Unable to create product.', 500);
        }
    }

    public function update(int $id): void
    {
        if ($this->products->findById($id) === null) {
            JsonResponse::error('Product not found.', 404);
            return;
        }

        try {
            $payload = $this->validatedPayload($this->readJsonBody());
            $this->products->update($id, $payload);
            $product = $this->products->findById($id);

            JsonResponse::send($this->present($product ?? []));
        } catch (InvalidArgumentException $exception) {
            JsonResponse::error($exception->getMessage(), 422);
        } catch (Throwable $exception) {
            JsonResponse::error('Unable to update product.', 500);
        }
    }

    public function destroy(int $id): void
    {
        if (!$this->products->delete($id)) {
            JsonResponse::error('Product not found.', 404);
            return;
        }

        JsonResponse::send(['message' => 'Product deleted successfully.']);
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
            throw new InvalidArgumentException('Invalid JSON body.');
        }

        return $decoded;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{nombre: string, descripcion: string, precio: float}
     */
    private function validatedPayload(array $payload): array
    {
        $nombre = trim((string) ($payload['nombre'] ?? ''));
        $descripcion = trim((string) ($payload['descripcion'] ?? ''));
        $precio = $payload['precio'] ?? null;

        if ($nombre === '') {
            throw new InvalidArgumentException('Field "nombre" is required.');
        }

        if ($descripcion === '') {
            throw new InvalidArgumentException('Field "descripcion" is required.');
        }

        if (!is_numeric($precio) || (float) $precio < 0) {
            throw new InvalidArgumentException('Field "precio" must be a non-negative number.');
        }

        return [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => (float) $precio,
        ];
    }
}
