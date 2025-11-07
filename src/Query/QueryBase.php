<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\OrmManager;
use Sarue\Orm\Query\Condition\ConditionGroupBase;

class QueryBase extends ConditionGroupBase implements QueryInterface
{
    final public function __construct(
        protected OrmManager $ormManager,
    ) {}
}
