<?php

namespace Sarue\Orm\Query\Condition;

abstract class AbstractCondition implements ConditionInterface
{
    final public string $fieldName {
        set(string $fieldName) {
            if (isset($this->fieldName)) {
                throw new \LogicException('Cannot alter property fieldName.');
            }

            $this->fieldName = $fieldName;
        }
    }
}
