<?php

namespace Sarue\Orm\Entity;

interface EntityInterface
{
    public static function fromDatabaseValues(array $values): static;

    public static function isRevisionable(): bool;

    public function initializeId(string $id): void;

    public function isNew(): bool;

    public function mayDelete(): bool;
}
