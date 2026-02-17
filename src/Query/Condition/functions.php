<?php

namespace Sarue\Orm\Query\Condition;

use BcMath\Number;
use Sarue\Orm\Query\Condition\Numeric\NumericConditionInterface;
use Sarue\Orm\Query\Condition\Numeric\SimpleNumericCondition;
use Sarue\Orm\Query\Condition\Operator\ComparisonOperator;
use Sarue\Orm\Query\Condition\Text\PatternMatchingCondition;
use Sarue\Orm\Query\Condition\Text\TextConditionInterface;
use Sarue\Orm\Query\Condition\Text\UnescapedText;

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
function startsWith(string $text, bool $caseInsensitive = false): TextConditionInterface
{
    return PatternMatchingCondition::startsWith($text, $caseInsensitive);
}

function doesNotStartWith(string $text, bool $caseInsensitive = false): TextConditionInterface
{
    return PatternMatchingCondition::doesNotStartWith($text, $caseInsensitive);
}

function endsWith(string $text, bool $caseInsensitive = false): TextConditionInterface
{
    return PatternMatchingCondition::endsWith($text, $caseInsensitive);
}

function doesNotEndWith(string $text, bool $caseInsensitive = false): TextConditionInterface
{
    return PatternMatchingCondition::doesNotEndWith($text, $caseInsensitive);
}

function contains(string $text, bool $caseInsensitive = false): TextConditionInterface
{
    return PatternMatchingCondition::contains($text, $caseInsensitive);
}

function doesNotContain(string $text, bool $caseInsensitive = false): TextConditionInterface
{
    return PatternMatchingCondition::doesNotContain($text, $caseInsensitive);
}

function like(string|UnescapedText $text, bool $caseInsensitive = false): TextConditionInterface
{
    return PatternMatchingCondition::like($text, $caseInsensitive);
}

function notLike(string|UnescapedText $text, bool $caseInsensitive = false): TextConditionInterface
{
    return PatternMatchingCondition::notLike($text, $caseInsensitive);
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
