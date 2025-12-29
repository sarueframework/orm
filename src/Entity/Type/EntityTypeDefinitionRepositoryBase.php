<?php

namespace Sarue\Orm\Entity\Type;

use Sarue\Orm\Field\Type\FieldTypeInterface;

abstract class EntityTypeDefinitionRepositoryBase implements EntityTypeDefinitionRepositoryInterface
{
    public function getEntityTypeDefinition(string $entityClass): EntityType
    {
        return $this->getEntityTypeDefinitions()[$entityClass] ?? throw new \Exception("Entity {$entityClass} not found.");
    }

    public function getFieldDefinitions(string $entityClass): array
    {
        return $this->getEntityTypeDefinition($entityClass)->fields;
    }

    public function getFieldDefinition(string $entityClass, string $fieldName): FieldTypeInterface
    {
        return $this->getEntityTypeDefinition($entityClass)->fields[$fieldName] ?? throw new \Exception("Field {$fieldName} not found in entity {$entityClass}.");;
    }
}
