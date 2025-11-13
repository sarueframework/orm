<?php

namespace Sarue\Orm\Query\Condition;

interface ConditionInterface
{
    /**
     * @return Array<string|>
     */
    public function buildSql(): array;
}
