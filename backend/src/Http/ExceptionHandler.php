<?php

declare(strict_types=1);

namespace App\Http;

use App\Exceptions\HttpException;
use Throwable;

final class ExceptionHandler
{
    public function handle(Throwable $exception): void
    {
        if ($exception instanceof HttpException) {
            JsonResponse::send(
                ApiError::fromHttpException($exception)->toArray(),
                $exception->status(),
            );
            return;
        }

        JsonResponse::send(ApiError::serverError()->toArray(), 500);
    }
}
