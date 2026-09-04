<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Config\Database;
use PDO;

final class ProductRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    /** @return list<array<string, mixed>> */
    public function findAll(): array
    {
        $statement = $this->db->query(
            'SELECT id, nombre, descripcion, precio, created_at, updated_at
             FROM productos
             ORDER BY id ASC'
        );

        return $statement->fetchAll();
    }

    /** @return array{items: list<array<string, mixed>>, total: int} */
    public function findPaginated(int $page, int $perPage): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $countStatement = $this->db->query('SELECT COUNT(*) FROM productos');
        $total = (int) $countStatement->fetchColumn();

        $statement = $this->db->prepare(
            'SELECT id, nombre, descripcion, precio, created_at, updated_at
             FROM productos
             ORDER BY id ASC
             LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue('limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return [
            'items' => $statement->fetchAll(),
            'total' => $total,
        ];
    }

    /** @return array<string, mixed>|null */
    public function findById(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT id, nombre, descripcion, precio, created_at, updated_at
             FROM productos
             WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $product = $statement->fetch();

        return $product === false ? null : $product;
    }

    /** @param array{nombre: string, descripcion: string, precio: float|int|string} $data */
    public function create(array $data): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO productos (nombre, descripcion, precio)
             VALUES (:nombre, :descripcion, :precio)'
        );
        $statement->execute([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'precio' => $data['precio'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** @param array{nombre: string, descripcion: string, precio: float|int|string} $data */
    public function update(int $id, array $data): void
    {
        $statement = $this->db->prepare(
            'UPDATE productos
             SET nombre = :nombre, descripcion = :descripcion, precio = :precio
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'precio' => $data['precio'],
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare('DELETE FROM productos WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }
}
