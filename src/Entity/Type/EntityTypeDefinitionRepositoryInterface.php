<?php

namespace Sarue\Orm\Entity\Type;

use Sarue\Orm\Field\Type\FieldTypeInterface;

interface EntityTypeDefinitionRepositoryInterface
{
    /**
     * @return array<string, EntityType>
     */
    public function getEntityTypeDefinitions(): array;

    public function getEntityTypeDefinition(string $entityClass): EntityType;

    /**
     * @return array<string, FieldTypeInterface>
     */
    public function getFieldDefinitions(string $entityClass): array;

    public function getFieldDefinition(string $entityClass, string $fieldName): FieldTypeInterface;
}
