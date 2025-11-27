<?php

namespace Sarue\Orm\Query\Condition;

use BcMath\Number;
use Sarue\Orm\Query\Condition\Numeric\NumericConditionInterface;
use Sarue\Orm\Query\Condition\Numeric\SimpleNumericCondition;
use Sarue\Orm\Query\Condition\Numeric\SimpleNumericConditionOperator;

// Numeric functions.
function isGreaterThan(int|Number $number): NumericConditionInterface
{
    return new SimpleNumericCondition($number, SimpleNumericConditionOperator::GreaterThan);
}

function isGreaterThanOrEqualTo(int|Number $number): NumericConditionInterface
{
    return new SimpleNumericCondition($number, SimpleNumericConditionOperator::GreaterThanOrEqualTo);
}

function isLessThan(int|Number $number): NumericConditionInterface
{
    return new SimpleNumericCondition($number, SimpleNumericConditionOperator::LessThan);
}

function isLessThanOrEqualTo(int|Number $number): NumericConditionInterface
{
    return new SimpleNumericCondition($number, SimpleNumericConditionOperator::LessThanOrEqualTo);
}

function isEqualTo(int|Number $number): NumericConditionInterface
{
    return new SimpleNumericCondition($number, SimpleNumericConditionOperator::EqualTo);
}

function isNotEqualTo(int|Number $number): NumericConditionInterface
{
    return new SimpleNumericCondition($number, SimpleNumericConditionOperator::NotEqualTo);
}
