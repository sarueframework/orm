<?php

namespace Sarue\Orm\Field\Type;

use Doctrine\DBAL\Query\QueryBuilder;
use Sarue\Orm\Entity\EntityInterface;

interface FieldTypeInterface
{
    public string $fieldName { set; }

    public string $propertyType { set; }

    public bool $required { set; }

    public function getConditionType(): string;

    /**
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    public function getSchema(): array;

    public function persistFieldToDatabase(QueryBuilder $queryBuilder, EntityInterface $entity): void;

    public function validateDefinition(): void;
}
