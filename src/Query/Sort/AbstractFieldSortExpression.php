<?php

namespace Sarue\Orm\Query\Sort;

abstract class AbstractFieldSortExpression implements FieldSortExpressionInterface
{
    protected string $fieldName;

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function setFieldName(string $fieldName): static
    {
        $this->fieldName = $fieldName;

        return $this;
    }
}
