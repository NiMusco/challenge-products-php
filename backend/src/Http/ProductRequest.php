<?php

declare(strict_types=1);

namespace App\Http;

use App\Exceptions\ValidationException;

final class ProductRequest
{
    private function __construct(
        private readonly string $nombre,
        private readonly string $descripcion,
        private readonly float $precio,
    ) {
    }

    public static function fromJsonBody(): self
    {
        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw === false ? '' : $raw, true);

        if (!is_array($decoded)) {
            throw new ValidationException('Invalid JSON body.', [
                'body' => 'Request body must be a valid JSON object.',
            ]);
        }

        return self::fromArray($decoded);
    }

    /** @param array<string, mixed> $payload */
    public static function fromArray(array $payload): self
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

        return new self($nombre, $descripcion, (float) $precio);
    }

    /** @return array{nombre: string, descripcion: string, precio: float} */
    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
        ];
    }
}
