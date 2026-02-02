<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\Query\Condition\ConditionInterface;

interface QueryInterface extends ConditionInterface
{
    public const string ENTITY_CLASS = '';

    public function loadById(string $id);

    public function loadAll();

    // public function count();
    // public function sum();
}
