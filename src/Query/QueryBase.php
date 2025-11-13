<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\OrmManager;
use Sarue\Orm\Query\Condition\Group\ConditionGroupBase;

abstract class QueryBase extends ConditionGroupBase implements QueryInterface
{
    final public function __construct(
        protected OrmManager $ormManager,
    ) {}

    protected function doLoadAll()
    {
        return $this->ormManager->loadAll($this);
    }
}
