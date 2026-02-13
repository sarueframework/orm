<?php

namespace Sarue\Orm\Query\Sort;

interface FieldSortExpressionInterface extends SortExpressionInterface
{
    public function getFieldName(): string;

    public function setFieldName(string $fieldName): static;
}
