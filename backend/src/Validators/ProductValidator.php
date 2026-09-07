<?php

declare(strict_types=1);

namespace App\Validators;

use App\Exceptions\ValidationException;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

final class ProductValidator
{
    public function validate(): array
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

    private function fromPayload(array $payload): array
    {
        $data = [
            'nombre' => trim((string) ($payload['nombre'] ?? '')),
            'descripcion' => trim((string) ($payload['descripcion'] ?? '')),
            'precio' => $payload['precio'] ?? null,
        ];

        try {
            v::key('nombre', v::stringType()->notEmpty())
                ->key('descripcion', v::stringType()->notEmpty())
                ->key('precio', v::numericVal()->min(0))
                ->assert($data);
        } catch (NestedValidationException $exception) {
            throw new ValidationException('Validation failed.', $this->detailsFromException($exception));
        }

        return [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'precio' => (float) $data['precio'],
        ];
    }

    private function detailsFromException(NestedValidationException $exception): array
    {
        $messages = $exception->getMessages([
            'nombre' => 'Field "nombre" is required.',
            'descripcion' => 'Field "descripcion" is required.',
            'precio' => 'Field "precio" must be a non-negative number.',
        ]);

        $details = [];
        foreach (['nombre', 'descripcion', 'precio'] as $field) {
            if (!isset($messages[$field])) {
                continue;
            }

            $message = $messages[$field];
            if (is_array($message)) {
                $message = (string) (array_values($message)[0] ?? '');
            }

            if ($message !== '') {
                $details[$field] = $message;
            }
        }

        return $details;
    }
}
