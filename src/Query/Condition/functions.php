<?php

namespace Sarue\Orm\Query\Condition;

use Sarue\Orm\Query\Condition\Numeric\NumericConditionInterface;
use Sarue\Orm\Query\Condition\Numeric\SimpleNumericCondition;
use Sarue\Orm\Query\Condition\Numeric\SimpleNumericConditionOperator;
use Sarue\Orm\Query\Condition\Text\LikeCondition;
use Sarue\Orm\Query\Condition\Text\TextConditionInterface;

// Numeric functions.
function isGreaterThan(float $number): NumericConditionInterface
{
    return new SimpleNumericCondition($number, SimpleNumericConditionOperator::GreaterThan);
}
function isLessThan(float $number): NumericConditionInterface
{
    return new SimpleNumericCondition($number, SimpleNumericConditionOperator::LessThan);
}

// Text functions.
function startsWith(string $string): TextConditionInterface
{
    return new LikeCondition("$string%");
}
