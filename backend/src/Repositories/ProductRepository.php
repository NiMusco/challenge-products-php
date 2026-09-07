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

    public function findAll(): array
    {
        $statement = $this->db->query(
            'SELECT id, nombre, descripcion, precio, created_at, updated_at
             FROM productos
             ORDER BY id ASC'
        );

        $items = [];
        foreach ($statement->fetchAll() as $row) {
            $items[] = $this->normalize($row);
        }

        return $items;
    }

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

        $items = [];
        foreach ($statement->fetchAll() as $row) {
            $items[] = $this->normalize($row);
        }

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    public function findById(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT id, nombre, descripcion, precio, created_at, updated_at
             FROM productos
             WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $product = $statement->fetch();

        return $product === false ? null : $this->normalize($product);
    }

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

    private function normalize(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'nombre' => (string) $row['nombre'],
            'descripcion' => (string) $row['descripcion'],
            'precio' => (float) $row['precio'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ];
    }
}
