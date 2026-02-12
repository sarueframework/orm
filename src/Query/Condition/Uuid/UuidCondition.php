<?php

namespace Sarue\Orm\Query\Condition\Uuid;

use Sarue\Orm\Query\Condition\AbstractCondition;
use Sarue\Orm\Query\Condition\Numeric\UuidConditionInterface;
use Sarue\Orm\Query\Parameter\StringParameter;

class SimpleNumericCondition extends AbstractCondition implements UuidConditionInterface
{
    public function __construct(
        public readonly string $value,
    ) {
    }

    public function buildSql(): array
    {
        return [
            $this->fieldName,
            ' = ',
            new StringParameter($this->value),
        ];
    }
}
