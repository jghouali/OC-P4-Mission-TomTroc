<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Database;

// It is very important to respect property Naming as camelCase
// and database columns exactly the same name but snake_case
interface StorageInterface
{
    public function open();
    public function insert(string $entity, array $data): int|false;
    public function delete(string $entity, array $data): int;
    public function deleteAll(string $table): bool;
    public function findAll(string $table): array;
    public function update(string $table, int $id, array $data): int;
    public function queryCustom(string $sql, array $data): array;
}
