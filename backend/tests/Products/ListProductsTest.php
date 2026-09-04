<?php

declare(strict_types=1);

it('lists products with pagination on GET /productos', function () {
    $response = $this->api('GET', '/productos?page=1&per_page=5');

    expect($response['status'])->toBe(200)
        ->and($response['body'])->toHaveKeys(['data', 'meta'])
        ->and($response['body']['data'])->toBeArray()
        ->and($response['body']['meta'])->toMatchArray([
            'page' => 1,
            'per_page' => 5,
        ])
        ->and($response['body']['meta'])->toHaveKeys(['total', 'total_pages']);

    if (count($response['body']['data']) > 0) {
        expect($response['body']['data'][0])->toHaveKeys([
            'id',
            'nombre',
            'descripcion',
            'precio',
            'precio_usd',
        ]);
    }
});

it('falls back to defaults for invalid pagination query on GET /productos', function () {
    $response = $this->api('GET', '/productos?page=abc&per_page=xyz');

    expect($response['status'])->toBe(200)
        ->and($response['body']['meta']['page'])->toBe(1)
        ->and($response['body']['meta']['per_page'])->toBe(5);
});

it('returns 404 for an unknown route', function () {
    $response = $this->api('GET', '/no-such-route');

    expect($response['status'])->toBe(404)
        ->and($response['body']['code'])->toBe('NOT_FOUND');
});

it('returns 405 for a disallowed method on /productos', function () {
    $response = $this->api('PATCH', '/productos');

    expect($response['status'])->toBe(405)
        ->and($response['body']['code'])->toBe('METHOD_NOT_ALLOWED');
});
