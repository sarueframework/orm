<?php

namespace Sarue\Orm\Query\Condition;

abstract class AbstractFieldCondition implements ConditionInterface, FieldConditionInterface
{
    protected readonly string $fieldName;

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function setFieldName(string $fieldName): static
    {
        $this->fieldName = $fieldName;

        return $this;
    }
}
