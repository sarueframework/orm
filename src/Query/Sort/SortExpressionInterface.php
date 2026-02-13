<?php

namespace Sarue\Orm\Query\Sort;

interface SortExpressionInterface
{
    public function sortExpression(): string;

    public function sortOrder(): SortOrder;
}
