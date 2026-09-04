<?php

declare(strict_types=1);

it('gets a product by id on GET /productos/{id}', function () {
    $created = $this->api('POST', '/productos', $this->productPayload());
    expect($created['status'])->toBe(201);

    $id = $created['body']['id'];
    $response = $this->api('GET', '/productos/' . $id);

    expect($response['status'])->toBe(200)
        ->and($response['body']['id'])->toBe($id)
        ->and($response['body'])->toHaveKeys([
            'nombre',
            'descripcion',
            'precio',
            'precio_usd',
            'created_at',
            'updated_at',
        ]);
});

it('returns 404 for a missing product on GET /productos/{id}', function () {
    $response = $this->api('GET', '/productos/999999');

    expect($response['status'])->toBe(404)
        ->and($response['body']['code'])->toBe('NOT_FOUND')
        ->and($response['body']['error'])->toBe('Product not found.');
});

it('returns 404 for a non-numeric product id on GET /productos/{id}', function () {
    $response = $this->api('GET', '/productos/abc');

    expect($response['status'])->toBe(404)
        ->and($response['body']['code'])->toBe('NOT_FOUND');
});
