<?php

namespace Sarue\Orm\Query\Condition;

abstract class AbstractFieldCondition implements ConditionInterface, FieldConditionInterface
{
    protected string $fieldName;

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function setFieldName(string $fieldName): static
    {
        if (isset($this->fieldName)) {
            throw new \LogicException('Cannot override fieldName of a condition.');
        }

        $this->fieldName = $fieldName;

        return $this;
    }
}
