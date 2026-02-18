<?php

namespace Sarue\Orm\Query\Condition;

use Sarue\Orm\Query\Parameter\ParameterInterface;

interface ConditionInterface
{
    /**
     * @return array<string|ParameterInterface>
     */
    public function buildSql(): array;
}
