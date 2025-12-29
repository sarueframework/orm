<?php

namespace Sarue\Orm\Field\Type;

use Doctrine\DBAL\Query\QueryBuilder;
use Sarue\Orm\Entity\EntityInterface;

interface FieldTypeInterface
{
    public function initializeDefinition(string $fieldName, string $propertyType): static;

    public function getFieldName(): string;

    public function getPropertyType(): string;

    public function isRequired(): bool;

    public function fromDatabaseValue(mixed $databaseValue): mixed;

    public function persistFieldToDatabase(QueryBuilder $queryBuilder, EntityInterface $entity): void;

    public function getConditionType(): string;

    /**
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    public function createSchema(): array;

    public function validateDefinition(): void;
}
