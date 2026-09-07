<?php

declare(strict_types=1);

it('lists products with pagination on GET /productos', function () {
    $response = $this->api('GET', '/productos?page=1&per_page=5');

    expect($response['status'])->toBe(200)
        ->and($response['body'])->toHaveKeys(['data'])
        ->and($response['body']['data'])->toBeArray()
        ->and($response['headers'])->toMatchArray([
            'x-page' => '1',
            'x-per-page' => '5',
        ])
        ->and($response['headers'])->toHaveKeys(['x-total-count', 'x-total-pages']);

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

it('clamps invalid pagination query values on GET /productos', function () {
    $response = $this->api('GET', '/productos?page=abc&per_page=xyz');

    expect($response['status'])->toBe(200)
        ->and($response['headers']['x-page'])->toBe('1')
        ->and($response['headers']['x-per-page'])->toBe('1');
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
