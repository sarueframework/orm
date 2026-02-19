<?php

namespace Sarue\Orm\Field\Type;

use Sarue\Orm\Query\Parameter\ParameterInterface;

interface FieldTypeInterface
{
    public function initializeDefinition(string $fieldName, string $propertyType): static;

    /**
     * @return non-empty-string
     */
    public function getFieldName(): string;

    public function getPropertyType(): string;

    public function isRequired(): bool;

    public function fromDatabaseValue(mixed $databaseValue): mixed;

    /**
     * @return array<string,ParameterInterface>
     */
    public function toDatabaseValues(mixed $fieldValue): array;

    public function getConditionType(): string;

    /**
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    public function createSchema(): array;

    public function validateDefinition(): void;
}
