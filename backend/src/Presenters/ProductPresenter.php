<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Services\PriceConverter;

final class ProductPresenter
{
    public function __construct(
        private readonly PriceConverter $priceConverter,
    ) {
    }

    /** @param array<string, mixed> $product */
    public function present(array $product): array
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

    /**
     * @param list<array<string, mixed>> $products
     * @return list<array<string, mixed>>
     */
    public function collection(array $products): array
    {
        return array_map(
            fn (array $product): array => $this->present($product),
            $products
        );
    }
}
