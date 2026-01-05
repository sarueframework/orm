<?php

namespace Sarue\Orm\Field\Type;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnEditor;
use Sarue\Orm\Entity\EntityInterface;

abstract class ScalarFieldTypeBase extends FieldTypeBase
{
    public const string COLUMN_TYPE = '';

    public function createSchema(): array
    {
        return [$this->getColumnEditor()->create()];
    }

    public function persistFieldToDatabase(QueryBuilder $queryBuilder, EntityInterface $entity): void
    {
        if (isset($entity->{$this->fieldName})) {
            $queryBuilder->setValue($this->fieldName, $queryBuilder->createNamedParameter($entity->{$this->fieldName}));
        }
        elseif ($this->isRequired()) {
            throw new \Exception("Required field '{$this->fieldName}' is not set.");
        }
    }

    protected function getColumnEditor(): ColumnEditor
    {
        return Column::editor()
            ->setUnquotedName($this->fieldName)
            ->setTypeName(static::COLUMN_TYPE)
            ->setNotNull($this->required);
    }
}
