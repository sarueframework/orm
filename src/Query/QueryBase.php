<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\Query\Condition\ConditionInterface;

class QueryBase implements QueryInterface
{
    public function addCondition(ConditionInterface $condition): static
    {
        return $this;
    }
}
