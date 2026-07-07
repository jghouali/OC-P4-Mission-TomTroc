<?php

declare(strict_types=1);

namespace Green\TomTroc\Entity;

interface EntityInterface
{
    public function toArray(): array;
    public static function getStorageIdName(): string;
    public function getId(): ?int;
}
