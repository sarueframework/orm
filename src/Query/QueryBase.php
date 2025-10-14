<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\Query\Condition\ConditionInterface;

class QueryBase implements ConditionInterface
{
    public function addCondition(ConditionInterface $condition): static
    {
        return $this;
    }
}
