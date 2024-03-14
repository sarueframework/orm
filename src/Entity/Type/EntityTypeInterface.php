<?php

namespace Sarue\Server\SarueServerBundle\Entity;

use Sarue\Orm\Field\FieldInterface;

interface EntityTypeInterface
{
    public static function createFromDefinition(string $entityTypeName, array $definition): static;

    public static function createFromSchemaStorage(string $entityTypeName, array $fieldsFromStorage): static;

    public function getName(): string;

    public function getField(string $fieldName): FieldInterface;

    public function getFields(): array;

    public function getHash(): string;
}
