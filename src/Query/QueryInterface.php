<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\Query\Condition\ConditionInterface;
use Sarue\Orm\Query\Sort\SortExpressionInterface;

interface QueryInterface extends ConditionInterface
{
    public function hasWhere(): bool;

    /**
     * @return SortExpressionInterface[]
     */
    public function getSortExpressions(): array;

    public function loadById(string $id);

    public function loadAll();

    // public function count();
    // public function sum();
}
