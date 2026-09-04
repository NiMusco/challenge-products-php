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
