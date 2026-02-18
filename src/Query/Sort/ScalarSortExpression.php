<?php

namespace Sarue\Orm\Query\Sort;

class ScalarSortExpression extends AbstractFieldSortExpression
{
    public function __construct(
        protected SortOrder $sortOrder,
    ) {
    }

    public function sortExpression(): string
    {
        return $this->fieldName;
    }

    public function sortOrder(): SortOrder
    {
        return $this->sortOrder;
    }
}
