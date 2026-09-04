<?php

declare(strict_types=1);

it('deletes a product on DELETE /productos/{id}', function () {
    $created = $this->api('POST', '/productos', $this->productPayload());
    expect($created['status'])->toBe(201);

    $id = $created['body']['id'];
    $response = $this->api('DELETE', '/productos/' . $id);

    expect($response['status'])->toBe(204)
        ->and($response['raw'])->toBe('');

    $missing = $this->api('GET', '/productos/' . $id);
    expect($missing['status'])->toBe(404);
});

it('returns 404 when deleting a missing product on DELETE /productos/{id}', function () {
    $response = $this->api('DELETE', '/productos/999999');

    expect($response['status'])->toBe(404)
        ->and($response['body']['code'])->toBe('NOT_FOUND');
});
