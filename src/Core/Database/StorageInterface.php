<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Database;

// It is very important to respect property Naming as camelCase
// and database columns exactly the same name but snake_case
interface StorageInterface
{
    public function open();
    public function insert(string $entity, array $data);
    public function delete(string $entity, array $data);
    public function deleteAll(string $table);
    public function findAll(string $table);
    public function findAllWhere(string $table, string $column, string $operator, string $value);
    public function findOne(string $table, string $column, mixed $value): array;
    public function update(string $table, int $id, array $data): bool;
}
