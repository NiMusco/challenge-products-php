<?php

declare(strict_types=1);

namespace App\Http;

/** Validated product payload (built by ValidateProductBody). */
final class ProductRequest
{
    public function __construct(
        private readonly string $nombre,
        private readonly string $descripcion,
        private readonly float $precio,
    ) {
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
