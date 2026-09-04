<?php

declare(strict_types=1);

it('creates a product on POST /productos', function () {
    $payload = $this->productPayload();
    $response = $this->api('POST', '/productos', $payload);

    expect($response['status'])->toBe(201)
        ->and($response['body']['nombre'])->toBe($payload['nombre'])
        ->and($response['body']['descripcion'])->toBe($payload['descripcion'])
        ->and($response['body']['precio'])->toBe($payload['precio'])
        ->and($response['body'])->toHaveKey('precio_usd')
        ->and($response['body']['id'])->toBeInt();
});

it('validates required fields on POST /productos', function () {
    $response = $this->api('POST', '/productos', [
        'nombre' => '',
        'descripcion' => '',
        'precio' => -10,
    ]);

    expect($response['status'])->toBe(422)
        ->and($response['body']['code'])->toBe('VALIDATION_ERROR')
        ->and($response['body']['error'])->toBe('Validation failed.')
        ->and($response['body']['details'])->toHaveKeys(['nombre', 'descripcion', 'precio']);
});

it('rejects invalid JSON on POST /productos', function () {
    $response = $this->api('POST', '/productos', '{not-valid-json');

    expect($response['status'])->toBe(422)
        ->and($response['body']['code'])->toBe('VALIDATION_ERROR')
        ->and($response['body']['details'])->toHaveKey('body');
});

it('rejects a non-numeric precio on POST /productos', function () {
    $response = $this->api('POST', '/productos', [
        'nombre' => 'Valid name',
        'descripcion' => 'Valid description',
        'precio' => 'barato',
    ]);

    expect($response['status'])->toBe(422)
        ->and($response['body']['code'])->toBe('VALIDATION_ERROR')
        ->and($response['body']['details'])->toHaveKey('precio')
        ->and($response['body']['details'])->not->toHaveKeys(['nombre', 'descripcion']);
});
