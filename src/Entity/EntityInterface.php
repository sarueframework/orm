<?php

namespace Sarue\Orm\Entity;

use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Field\Type\FieldTypeInterface;

interface EntityInterface
{
    public static function getTypeDefinition(): EntityType;

    public static function getFieldDefinitions(): array;

    public static function getFieldDefinition(string $fieldName): FieldTypeInterface;

    public static function fromDatabaseValues(array $values): static;

    public function id(): string;

    /**
     * @return array<string,\Sarue\Orm\Query\Parameter\ParameterInterface>
     */
    public function toDatabaseValues(): array;

    public function save(): void;

    public function initializeId(string $id): void;

    public function isNew(): bool;

    public function assertInsertAccess(): void;

    public function assertUpdateAccess(): void;

    public function assertDeleteAccess(): void;
}
