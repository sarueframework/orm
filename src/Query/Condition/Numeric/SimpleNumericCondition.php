<?php

namespace Sarue\Orm\Query\Condition\Numeric;

use BcMath\Number;
use Sarue\Orm\Query\Condition\AbstractFieldCondition;
use Sarue\Orm\Query\Parameter\IntegerParameter;
use Sarue\Orm\Query\Parameter\NumberParameter;

class SimpleNumericCondition extends AbstractFieldCondition implements NumericConditionInterface
{
    public function __construct(
        public readonly int|Number $number,
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
            ($this->number instanceof Number) ? new NumberParameter($this->number) : new IntegerParameter($this->number),
        ];
    }
}
