<?php

declare(strict_types=1);

it('updates a product on PUT /productos/{id}', function () {
    $created = $this->api('POST', '/productos', $this->productPayload());
    expect($created['status'])->toBe(201);

    $id = $created['body']['id'];
    $payload = [
        'nombre' => 'Updated Product',
        'descripcion' => 'Updated by Pest',
        'precio' => 22000.0,
    ];

    $response = $this->api('PUT', '/productos/' . $id, $payload);

    expect($response['status'])->toBe(200)
        ->and($response['body']['id'])->toBe($id)
        ->and($response['body']['nombre'])->toBe('Updated Product')
        ->and($response['body']['descripcion'])->toBe('Updated by Pest')
        ->and($response['body']['precio'])->toBe(22000)
        ->and($response['body'])->toHaveKey('precio_usd');
});

it('returns 404 when updating a missing product on PUT /productos/{id}', function () {
    $response = $this->api('PUT', '/productos/999999', $this->productPayload());

    expect($response['status'])->toBe(404)
        ->and($response['body']['code'])->toBe('NOT_FOUND');
});
