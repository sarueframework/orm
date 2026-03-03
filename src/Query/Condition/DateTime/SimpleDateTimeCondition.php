<?php

namespace Sarue\Orm\Query\Condition\DateTime;

use Sarue\Orm\Query\Condition\AbstractFieldCondition;
use Sarue\Orm\Query\Condition\Operator\ComparisonOperator;
use Sarue\Orm\Query\Parameter\TextParameter;

class SimpleDateTimeCondition extends AbstractFieldCondition implements DateTimeConditionInterface
{
    public static function isBefore(\DateTime $dateTime): self
    {
        return new self($dateTime, ComparisonOperator::LessThan);
    }

    public static function isBeforeOrExactly(\DateTime $dateTime): self
    {
        return new self($dateTime, ComparisonOperator::LessThanOrEqualTo);
    }

    public static function isExactly(\DateTime $dateTime): self
    {
        return new self($dateTime, ComparisonOperator::EqualTo);
    }

    public static function isNot(\DateTime $dateTime): self
    {
        return new self($dateTime, ComparisonOperator::NotEqualTo);
    }

    public static function isAfter(\DateTime $dateTime): self
    {
        return new self($dateTime, ComparisonOperator::GreaterThan);
    }

    public static function isAfterOrExactly(\DateTime $dateTime): self
    {
        return new self($dateTime, ComparisonOperator::GreaterThanOrEqualTo);
    }

    public function __construct(
        public readonly \DateTime $dateTime,
        public readonly ComparisonOperator $operator,
    ) {
    }

    public function buildSql(): array
    {
        return [
            $this->fieldName,
            $this->operator->value,
            new TextParameter($this->dateTime->format('Y-m-d H:i:s')),
        ];
    }
}
