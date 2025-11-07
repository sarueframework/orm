<?php

namespace Sarue\Orm\Query\Condition;

interface ConditionInterface
{
    public string $fieldName { set; }

    //public function getWhere(): string;
    //public function getWhereParameters(): array;
}
