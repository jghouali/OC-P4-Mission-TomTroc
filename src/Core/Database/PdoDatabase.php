<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Database;

use PDO;
use RuntimeException;

// This class implement StorageInterface as it will be possible
// to change it with every class that implement StorageInterface,
// like Json, API, etc...
// It is very important to respect property Naming as camelCase
// and database columns exactly the same name but snake_case
class PdoDatabase implements StorageInterface
{
    private static PDO $pdo;
    private string $dsn;
    private string $user;
    private string $password;
    private ?array $options;
    private ?int $fetchAllMode;
    private ?int $fetchMode;

    public function __construct(
        string $dsn,
        string $user,
        string $password,
        ?array $options,
        ?int $fetchAllMode,
        ?int $fetchMode
    ) {
        $this->dsn = $dsn;
        $this->user = $user;
        $this->password = $password;
        $this->options = $options;
        $this->fetchAllMode = $fetchAllMode;
        $this->fetchMode = $fetchMode;
    }

    public function open()
    {
        // we keek this $pdo static
        if (!isset(self::$pdo)) {
            self::$pdo = PDO::connect(
                $this->dsn,
                $this->user,
                $this->password,
                $this->options
            );
        }
    }

    // This function get the entity to insert as an array,
    // unpack and insert it in db
    public function insert(string $entity, array $data): int|false
    {
        $params = [];
        $columns = [];
        $valuesArray = [];

        // pop the last row entity_id since we insert()
        array_pop($data);

        foreach ($data as $column => $value) {
            // prefix with ':' the column
            $valuesArray[] = ":$column";
            // put the column in columns
            $columns[] = $column;
            // put the value in params
            $params[":$column"] = $value;
        }

        // imlode it with ', '
        $columns = implode(', ', $columns);
        $valuesString = implode(', ', $valuesArray);

        $sql = "INSERT INTO $entity ($columns) VALUES ($valuesString)";

        // we can now safely prepare the statement
        $statement = self::$pdo->prepare("$sql");

        $statement->execute($params);
        $rowAffected = $statement->rowCount();

        if ($rowAffected === 1) {
            return (int) self::$pdo->lastInsertId();
        } elseif ($rowAffected > 1) {
            throw new RuntimeException('More than 1 affected rows', 500);
        }
        return false;
    }

    // This function get the entity to update as an array,
    // unpack and update it in db
    public function update(string $entity, int $id, array $data): int
    {
        $setArray = [];
        $params = [];

        $primary = substr($entity, 0, strlen($entity) - 1) . '_id';

        foreach ($data as $column => $value) {
            if ($column === $primary) {
                continue;
            }
            $setArray[] = "$column = :$column";
            $params[":$column"] = $value;
        }
        $setString = implode(', ', $setArray);

        $sql = "UPDATE $entity SET $setString WHERE $primary = :primary_id";

        $params[':primary_id'] = $id;

        $statement = self::$pdo->prepare($sql);
        $statement->execute($params);

        return $statement->rowCount();
    }

    // This function get the entity to delete as an array,
    // unpack and delete it in db
    public function delete(string $entity, array $data): int
    {
        $whereArray = [];
        $params = [];

        foreach ($data as $column => $value) {
            $whereArray[] = "$column = :$column";
            $params[":$column"] = $value;
        }
        $whereString = implode(' AND ', $whereArray);

        $sql = "DELETE FROM $entity WHERE $whereString";
        $statement = self::$pdo->prepare("$sql");

        $statement->execute($params);

        return $statement->rowCount();
    }

    // Danger This function delete all the table
    public function deleteAll(string $entity): bool
    {
        $sql = "DELETE FROM $entity";
        $statement = self::$pdo->prepare("$sql");

        $statement->execute();

        if ($statement->rowCount() > 0) {
            return true;
        }
        return false;
    }

    // This function search for all entity in table
    public function findAll(string $entity): array
    {
        $statement = self::$pdo->prepare("SELECT * FROM $entity");

        $statement->execute();

        return $statement->fetchAll($this->fetchAllMode);
    }

    // This function search for all entity in table
    // that respond to the query-like given
    public function queryCustom(string $sql, array $data): array
    {
        preg_match_all('/:([\w]+)/', $sql, $matches);

        foreach ($matches[1] as $key) {
            if (!array_key_exists($key, $data)) {
                throw new RuntimeException("Bind value :$key not present in data");
            }
        }

        $statement = self::$pdo->prepare($sql);
        $allowedType = [
            PDO::PARAM_BOOL,
            PDO::PARAM_INT,
            PDO::PARAM_STR,
            PDO::PARAM_NULL,
        ];

        foreach ($data as $key => [$value, $type]) {
            if (!in_array($type, $allowedType, true)) {
                throw new RuntimeException("Type :$type not allowed for key $key");
            }
            $statement->bindValue(':' . $key, $value, $type);
        }

        $statement->execute();
        return $statement->fetchAll($this->fetchAllMode);
    }
}
