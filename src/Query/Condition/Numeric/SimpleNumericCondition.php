<?php

namespace Sarue\Orm\Query\Condition\Numeric;

use Sarue\Orm\Query\Condition\Numeric\NumericConditionInterface;

class SimpleNumericCondition implements NumericConditionInterface {
    public function __construct(
        public float $number,
        public string $operator,
    ) {}
}
