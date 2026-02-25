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
            new TextParameter($this->dateTime->format('Y-m-d')),
        ];
    }
}
