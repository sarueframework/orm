<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\OrmManager;
use Sarue\Orm\Query\Condition\Group\ConditionGroupBase;

abstract class AbstractQuery extends ConditionGroupBase implements QueryInterface
{
    final public function __construct(
        protected OrmManager $ormManager,
    ) {
    }

    protected function doLoadById(string $id)
    {
        return $this->ormManager->loadById($this, $id);
    }

    protected function doLoadAll()
    {
        return $this->ormManager->loadAll($this);
    }
}
