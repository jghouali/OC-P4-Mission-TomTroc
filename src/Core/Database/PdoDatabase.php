<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Database;

use Exception;
use PDO;
use RuntimeException;

// This class implement StorageInterface as it will be possible
// to change it with every class implement StorageInterface,
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

        // pop the entity_id since we insert()
        array_pop($data);

        foreach ($data as $column => $value) {
            $valuesArray[] = ":$column";
            $columns[] = $column;
            $params[":$column"] = $value;
        }

        $columns = implode(', ', $columns);
        $valuesString = implode(', ', $valuesArray);

        $sql = "INSERT INTO $entity ($columns) VALUES ($valuesString)";

        $statement = self::$pdo->prepare("$sql");

        if ($statement->execute($params)) {
            return (int) self::$pdo->lastInsertId();
        }
        return false;
    }

    // This function get the entity to update as an array,
    // unpack and update it in db
    public function update(string $entity, int $id, array $data): bool
    {
        $setArray = [];
        $params = [];

        foreach ($data as $column => $value) {
            $setArray[] = "$column = :$column";
            $params[":$column"] = $value;
        }

        $primary = substr($entity, 0, strlen($entity) - 1) . '_id';
        $setString = implode(', ', $setArray);

        $sql = "UPDATE $entity SET $setString WHERE $primary = :primary_id";

        $params[':primary_id'] = $id;

        $statement = self::$pdo->prepare($sql);
        return $statement->execute($params);
    }

    // This function get the entity to delete as an array,
    // unpack and delete it in db
    public function delete(string $entity, array $data): bool
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

        return $statement->execute($params);
    }

    // This function delete all the table
    public function deleteAll(string $entity): bool
    {
        $sql = "DELETE FROM $entity";
        $statement = self::$pdo->prepare("$sql");

        return $statement->execute();
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

    // This function search for One entity in table
    // that respond to the query-like given
    public function findOne(string $table, string $column, mixed $value): array
    {
        $sql = "SELECT * FROM $table WHERE $column = :value";

        $statement = self::$pdo->prepare("$sql");
        $statement->execute([
            ':value' => $value,
        ]);

        try {
            $result = $statement->fetch($this->fetchMode);
        } catch (Exception $e) {
            throw new RuntimeException("SELECT * FROM $table WHERE $column = '$value' return  : " . $e->getMessage());
        }
        if (!$result) {
            $result = [];
        }
        return $result;
    }

    // We replace camelCase property to snake_case sql column
    public function camelToSnake(string $camelCase)
    {
        $regex = '/(?<=\w)(?=[A-Z])|(?<=[a-z])(?=[0-9])/';
        $snakeCase = strtolower(preg_replace($regex, '_', $camelCase));

        return $snakeCase;
    }
}
