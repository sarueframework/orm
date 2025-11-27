<?php

namespace Sarue\Orm\Query\Condition\Numeric;

use Sarue\Orm\Query\Condition\ConditionBase;
use Sarue\Orm\Query\Parameter;

class SimpleNumericCondition extends ConditionBase implements NumericConditionInterface
{
    public function __construct(
        public readonly float $number,
        public readonly SimpleNumericConditionOperator $operator,
    ) {
    }

    public function buildSql(): array
    {
        return [
            $this->fieldName,
            match ($this->operator) {
                SimpleNumericConditionOperator::GreaterThan => '>',
                SimpleNumericConditionOperator::GreaterThanOrEqualTo => '>=',
                SimpleNumericConditionOperator::LessThan => '<',
                SimpleNumericConditionOperator::LessThanOrEqualTo => '<=',
                SimpleNumericConditionOperator::EqualTo => '=',
                SimpleNumericConditionOperator::NotEqualTo => '<>',
            },
            new Parameter($this->number),
        ];
    }
}
