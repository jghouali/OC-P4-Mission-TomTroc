<?php

declare(strict_types=1);

namespace Green\TomTroc\Entity;

use RuntimeException;

abstract class AbstractEntity implements EntityInterface
{
    protected ?int $id = null;

    abstract public static function getStorageIdName(): string;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    // will serve call getUsername => return $this->username
    public function __call(string $method, array $args): mixed
    {
        if (str_starts_with($method, 'get')) {
            // Extract property name : getUsername → username
            $property = lcfirst(substr($method, 3));

            if (property_exists($this, $property)) {
                return $this->$property;
            }

            throw new RuntimeException(
                "Property '$property' does not exist on " . static::class
            );
        }

        throw new RuntimeException(
            "method '$method' does not exist on " . static::class
        );
    }

    public function securePrintText(string $string, ?int $trimNumber = 0): string
    {
        if ($trimNumber === 0) {
            return nl2br(
                htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                false
            );
        } elseif ($trimNumber >= 1) {
            return nl2br(
                htmlspecialchars(
                    mb_strimwidth($string, 0, $trimNumber, '...'),
                    ENT_QUOTES | ENT_SUBSTITUTE,
                    'UTF-8'
                ),
                false
            );
        }
        throw new RuntimeException('trimNumber must be positive', 400);
    }
}
