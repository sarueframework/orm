<?php

namespace Sarue\Orm\Query\Condition;

use LogicException;

abstract class ConditionBase implements ConditionInterface
{
    public final string $fieldName {
        set(string $fieldName) {
            if (isset($this->fieldName)) {
                throw new LogicException('Cannot alter property fieldName.');
            }

            $this->fieldName = $fieldName;
        }
    }
}
