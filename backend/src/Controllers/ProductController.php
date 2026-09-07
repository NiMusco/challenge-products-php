<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\HttpException;
use App\Http\JsonResponse;
use App\Http\Pagination;
use App\Repositories\ProductRepository;
use App\Utils\PriceConverter;
use App\Validators\ProductValidator;

final class ProductController
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly PriceConverter $priceConverter,
        private readonly ProductValidator $productValidator = new ProductValidator(),
    ) {
    }

    public function list(): void
    {
        $pagination = Pagination::buildFromQueryParams();
        $result = $this->products->find($pagination->page, $pagination->perPage);

        $items = [];
        foreach ($result['items'] as $product) {
            $items[] = $this->present($product);
        }

        $pagination->applyHeaders($result['total']);
        JsonResponse::send(['data' => $items]);
    }

    public function get(int $id): void
    {
        $product = $this->products->findById($id);

        if ($product === null) {
            throw new HttpException('Product not found.', 404, 'NOT_FOUND');
        }

        JsonResponse::send($this->present($product));
    }

    public function create(): void
    {
        $data = $this->productValidator->validate();
        $id = $this->products->create($data);
        $product = $this->products->findById($id);

        JsonResponse::send($this->present($product ?? []), 201);
    }

    public function update(int $id): void
    {
        $data = $this->productValidator->validate();

        $this->products->update($id, $data);
        $product = $this->products->findById($id);

        if ($product === null) {
            throw new HttpException('Product not found.', 404, 'NOT_FOUND');
        }

        JsonResponse::send($this->present($product));
    }

    public function delete(int $id): void
    {
        if (!$this->products->delete($id)) {
            throw new HttpException('Product not found.', 404, 'NOT_FOUND');
        }

        http_response_code(204);
    }

    private function present(array $product): array
    {
        $product['precio_usd'] = $this->priceConverter->toUsd($product['precio']);

        return $product;
    }
}
