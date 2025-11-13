<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\Query\Condition\ConditionInterface;

interface QueryInterface extends ConditionInterface
{
    const string ENTITY_CLASS = '';

    public function loadAll();

    // public function loadOne();
    // public function count();
    // public function sum();
}
