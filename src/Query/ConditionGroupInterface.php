<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\Query\Condition\ConditionInterface;

interface ConditionGroupInterface extends ConditionInterface
{
    /**
     * @return class-string
     */
    public function getEntityClass(): string;
}
