<?php

namespace Sarue\Orm\Entity;

use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Exception\MayNotDeleteException;
use Sarue\Orm\Exception\MayNotInsertException;
use Sarue\Orm\Exception\MayNotUpdateException;
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

    public function toDatabaseValues(): array
    {
        $databaseValues = [];
        foreach (static::getFieldDefinitions() as $fieldName => $fieldDefinition) {
            $databaseValues = array_merge($fieldDefinition->toDatabaseValues())
        }
    }

    final public function isNew(): bool
    {
        // @todo Allow for settings IDs in new entities.
        return !isset($this->id);
    }

    final public function initializeId(string $id): void
    {
        if (!$this->isNew()) {
            throw new \Exception('Cannot override ID.');
        }

        $this->id = $id;
    }


    public function assertInsertAccess(): void
    {
        if (!$this->mayInsert()) {
            throw new MayNotInsertException();
        }
    }

    public function assertUpdateAccess(): void
    {
        if (!$this->mayUpdate()) {
            throw new MayNotUpdateException();
        }
    }

    public function assertDeleteAccess(): void
    {
        if (!$this->mayDelete()) {
            throw new MayNotDeleteException();
        }
    }

    abstract protected function mayInsert(): bool;

    abstract protected function mayUpdate(): bool;

    abstract protected function mayDelete(): bool;
}
