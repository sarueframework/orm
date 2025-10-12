<?php

namespace Sarue\Orm\Query\Condition;

use Sarue\Orm\Query\Condition\Numeric\NumericConditionInterface;
use Sarue\Orm\Query\Condition\Numeric\SimpleNumericCondition;
use Sarue\Orm\Query\Condition\Text\LikeCondition;
use Sarue\Orm\Query\Condition\Text\TextConditionInterface;

// Numeric functions.
function isLargerThan(float $number): NumericConditionInterface {
    return new SimpleNumericCondition($number, '>');
}

// Text functions.
function startsWith(string $string): TextConditionInterface {
    return new LikeCondition("$string%");
}
