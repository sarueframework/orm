<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\Query\Condition\ConditionInterface;

class QueryBase
{
    public function addCondition(ConditionInterface $condition): static
    {
        return $this;
    }
}
