<?php

namespace Sarue\Orm\Query\Condition\Numeric;

class SimpleNumericCondition implements NumericConditionInterface
{
    public function __construct(
        public float $number,
        public string $operator,
    ) {
    }
}
