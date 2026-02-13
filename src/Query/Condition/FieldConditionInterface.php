<?php

namespace Sarue\Orm\Query\Condition;

interface FieldConditionInterface
{
    public function getFieldName(): string;

    public function setFieldName(string $fieldName): static;
}
