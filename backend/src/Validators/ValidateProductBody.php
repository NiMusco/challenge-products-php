<?php

declare(strict_types=1);

namespace App\Validators;

use App\Exceptions\ValidationException;
use App\Http\ProductRequest;

/**
 * Validates the JSON body for product create/update endpoints.
 */
final class ValidateProductBody
{
    public function validate(): ProductRequest
    {
        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw === false ? '' : $raw, true);

        if (!is_array($decoded)) {
            throw new ValidationException('Invalid JSON body.', [
                'body' => 'Request body must be a valid JSON object.',
            ]);
        }

        return $this->fromPayload($decoded);
    }

    /** @param array<string, mixed> $payload */
    private function fromPayload(array $payload): ProductRequest
    {
        $details = [];

        $nombre = trim((string) ($payload['nombre'] ?? ''));
        $descripcion = trim((string) ($payload['descripcion'] ?? ''));
        $precio = $payload['precio'] ?? null;

        if ($nombre === '') {
            $details['nombre'] = 'Field "nombre" is required.';
        }

        if ($descripcion === '') {
            $details['descripcion'] = 'Field "descripcion" is required.';
        }

        if (!is_numeric($precio) || (float) $precio < 0) {
            $details['precio'] = 'Field "precio" must be a non-negative number.';
        }

        if ($details !== []) {
            throw new ValidationException('Validation failed.', $details);
        }

        return new ProductRequest($nombre, $descripcion, (float) $precio);
    }
}
