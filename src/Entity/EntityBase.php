<?php

namespace Sarue\Orm\Entity;

use Sarue\Orm\Field\Type\FieldTypeInterface;
use Sarue\Orm\OrmManager;
use Sarue\Orm\Schema\EntityDefinition;

abstract class EntityBase implements EntityInterface
{
    public static function getDefinition(): EntityDefinition
    {
        return OrmManager::getInstance()->getEntityDefinition(static::class);
    }

    public static function getFieldDefinitions(): array
    {
        return OrmManager::getInstance()->getFieldDefinitions(static::class);
    }

    public static function getFieldDefinition(string $fieldName): FieldTypeInterface
    {
        return OrmManager::getInstance()->getFieldDefinition(static::class, $fieldName);
    }

    public static function fromDatabaseValues(array $values): static
    {
        $valuesGroupedByField = [];
        foreach ($values as $column => $value) {
            if (str_contains($column, '__')) {
                [$fieldName, $property] = explode('__', $column);
                $valuesGroupedByField[$fieldName][$property] = $value;
            }
            else {
                $valuesGroupedByField[$column] = $value;
            }
        }

        $entity = new static();

        foreach (static::getFieldDefinitions() as $fieldName => $fieldDefinition) {
            if (!array_key_exists($fieldName, $valuesGroupedByField)) {
                throw new \Exception("Field $fieldName was not provided.");
            }
            $entity->{$fieldName} = $fieldDefinition->fromDatabaseValue($valuesGroupedByField[$fieldName]);
        }

        return $entity;
    }
}
