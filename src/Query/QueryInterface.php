<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\Entity\EntityInterface;
use Sarue\Orm\Query\Condition\ConditionInterface;
use Sarue\Orm\Query\Sort\SortExpressionInterface;

interface QueryInterface extends ConditionInterface
{
    public function hasWhere(): bool;

    /**
     * @return SortExpressionInterface[]
     */
    public function getSortExpressions(): array;

    public function loadById(string $id): EntityInterface;

    /**
     * @return EntityInterface[]
     */
    public function loadAll(): array;

    // public function count();
    // public function sum();
}
