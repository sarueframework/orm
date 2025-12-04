<?php

namespace Sarue\Orm\Field\Type;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Column;
use Sarue\Orm\Entity\EntityInterface;

abstract class ScalarFieldTypeBase extends FieldTypeBase
{
    public const string COLUMN_TYPE = '';

    public function getSchema(): array
    {
        return [
            Column::editor()
                ->setUnquotedName($this->fieldName)
                ->setTypeName(static::COLUMN_TYPE)
                ->setNotNull($this->required)
                ->create(),
        ];
    }

    public function persistFieldToDatabase(QueryBuilder $queryBuilder, EntityInterface $entity): void
    {
        $queryBuilder->setValue($this->fieldName, $queryBuilder->createNamedParameter($entity->{$this->fieldName}));
    }
}
