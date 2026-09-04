<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class PriceConverter
{
    public function __construct(
        private readonly float $precioUsd,
    ) {
        if ($this->precioUsd <= 0) {
            throw new RuntimeException('PRECIO_USD must be a positive number.');
        }
    }

    public function toUsd(float|string $precioArs): float
    {
        return round(((float) $precioArs) / $this->precioUsd, 2);
    }
}
