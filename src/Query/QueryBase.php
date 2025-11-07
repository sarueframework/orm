<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\Query\Condition\ConditionInterface;

class QueryBase implements QueryInterface
{
    /**
     * @var \Sarue\Orm\Query\Condition\ConditionInterface[]
     */
    protected array $conditions;

    protected function addCondition(string $fieldName, ConditionInterface $condition): static
    {
        $condition->fieldName = $fieldName;
        $this->conditions[] = $condition;

        return $this;
    }
}
