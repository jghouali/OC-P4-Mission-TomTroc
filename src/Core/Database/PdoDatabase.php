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
        $columns = [];
        $values = [];

        // pop the entity_id since we insert()
        array_pop($data);

        foreach ($data as $column => $value) {
            $columns[] = $column;
            $values[] = $value;
        }

        $columns = implode(', ', $columns);
        $values = '(\'' . implode('\', \'', $values) . '\')';

        $sql = "INSERT INTO $entity ($columns) VALUES $values";
        $statement = self::$pdo->prepare("$sql");

        if ($statement->execute()) {
            return (int) self::$pdo->lastInsertId();
        }
        return false;
    }

    // This function get the entity to update as an array,
    // unpack and update it in db
    public function update(string $table, int $id, array $data): bool
    {
        $update = '';
        foreach ($data as $column => $value) {
            $update = $update . ' ' . $column . ' = \'' . $value . '\',';
        }

        $primary = substr($table, 0, strlen($table) - 1) . '_id';
        $update = substr($update, 0, strlen($update) - 1);

        $sql = "UPDATE $table SET $update WHERE $primary = $id";
        $statement = self::$pdo->prepare("$sql");

        return $statement->execute();
    }

    // This function get the entity to delete as an array,
    // unpack and delete it in db
    public function delete(string $entity, array $data): bool
    {
        $where = '';
        foreach ($data as $column => $value) {
            $where = $where . $column . '=\'' . $value . '\' AND ';
        }

        $sql = "DELETE FROM $entity WHERE $where";
        $sql = substr($sql, 0, strlen($sql) - 5);

        $statement = self::$pdo->prepare("$sql");

        return $statement->execute();
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
        $sql = "SELECT * FROM $table WHERE $column = '$value'";

        $statement = self::$pdo->prepare("$sql");
        $statement->execute();

        try {
            $result = $statement->fetch($this->fetchMode);
        } catch (Exception $e) {
            throw new RuntimeException("SELECT * FROM $table WHERE $column = '$value' return  : " . $e->getMessage());
        }
        if (is_array($result)) {
            if (count($result) === 0) {
                $error = 'no result';
            } else {
                return $result;
            }
        }
        if ($result === false) {
            $error = 'false';
        } else {
            $error = $result;
        }
        throw new RuntimeException("SELECT * FROM $table WHERE $column = '$value' return " . $error);
    }

    // We replace camelCase property to snake_case sql column
    public function camelToSnake(string $camelCase)
    {
        $regex = '/(?<=\w)(?=[A-Z])|(?<=[a-z])(?=[0-9])/';
        $snakeCase = strtolower(preg_replace($regex, '_', $camelCase));

        return $snakeCase;
    }
}
