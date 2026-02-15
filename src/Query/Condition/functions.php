<?php

namespace Sarue\Orm\Query\Condition;

use BcMath\Number;
use Sarue\Orm\Query\Condition\Numeric\NumericConditionInterface;
use Sarue\Orm\Query\Condition\Numeric\SimpleNumericCondition;
use Sarue\Orm\Query\Condition\Operator\ComparisonOperator;
use Sarue\Orm\Query\Condition\Text\TextConditionInterface;

// Numeric functions.
function isGreaterThan(int|Number $number): NumericConditionInterface
{
    return new SimpleNumericCondition($number, ComparisonOperator::GreaterThan);
}

function isGreaterThanOrEqualTo(int|Number $number): NumericConditionInterface
{
    return new SimpleNumericCondition($number, ComparisonOperator::GreaterThanOrEqualTo);
}

function isLessThan(int|Number $number): NumericConditionInterface
{
    return new SimpleNumericCondition($number, ComparisonOperator::LessThan);
}

function isLessThanOrEqualTo(int|Number $number): NumericConditionInterface
{
    return new SimpleNumericCondition($number, ComparisonOperator::LessThanOrEqualTo);
}

function isEqualTo(int|Number $number): NumericConditionInterface
{
    return new SimpleNumericCondition($number, ComparisonOperator::EqualTo);
}

function isNotEqualTo(int|Number $number): NumericConditionInterface
{
    return new SimpleNumericCondition($number, ComparisonOperator::NotEqualTo);
}

// Text conditions.
function startsWith(string $text): TextConditionInterface
{
}

function endsWith(string $text): TextConditionInterface
{
}

function contains(string $text): TextConditionInterface
{

}

function like(string $text): TextConditionInterface
{

}

function notLike(string $text): TextConditionInterface
{

}

function likeWithWildcards(string $textWithWildcards): TextConditionInterface
{

}

function notLikeWithWildcards(string $textWithWildcards): TextConditionInterface
{

}

function textIsExactly(string $text): TextConditionInterface
{
}

function textIsDifferentFrom(string $text): TextConditionInterface
{

}

function isAlphabeticallyBefore(string $text): TextConditionInterface
{

}

function isAlphabeticallyBeforeOrEqualTo(string $text): TextConditionInterface
{

}

function isAlphabeticallyAfter(string $text): TextConditionInterface
{
}

function isAlphabeticallyAfterOrEqualTo(string $text): TextConditionInterface
{
}
