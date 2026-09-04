<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class PriceConverter
{
    public function toUsd(float|string $precioArs): float
    {
        $precioUsdRate = (float) ($_ENV['PRECIO_USD'] ?? 0);

        if ($precioUsdRate <= 0) {
            throw new RuntimeException('PRECIO_USD must be a positive number.');
        }

        return round(((float) $precioArs) / $precioUsdRate, 2);
    }
}
