<?php

namespace Sarue\Orm\Entity;

use Ramsey\Uuid\Uuid;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Field\Type\FieldTypeInterface;
use Sarue\Orm\Field\Type\Uuid\UuidField;
use Sarue\Orm\OrmManager;

abstract class AbstractBaseEntity implements EntityInterface
{
    #[UuidField(
        generateIdByDefault: true,
    )]
    final public readonly ?string $id;

    public static function getTypeDefinition(): EntityType
    {
        return OrmManager::getInstance()->getEntityTypeDefinition(static::class);
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
            } else {
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

    final public function isNew(): bool
    {
        return !isset($this->id);
    }

    final public function initializeId(): void
    {
        if (!$this->isNew()) {
            throw new \Exception('Initializing already initialized ID');
        }

        $this->id = $this->generateId();
    }

    protected function generateId(): string
    {
        return Uuid::uuid4()->toString();
    }
}
