<?php

namespace Sarue\Orm\Query\Condition\Numeric;

use Sarue\Orm\Query\Condition\ConditionBase;

class SimpleNumericCondition extends ConditionBase implements NumericConditionInterface
{
    public function __construct(
        public float $number,
        public string $operator,
    ) {
    }
}
