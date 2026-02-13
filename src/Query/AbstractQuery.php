<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\Query\Condition\Group\AbstractConditionGroup;

abstract class AbstractQuery extends AbstractConditionGroup implements QueryInterface
{
    protected const array QUERY_METHODS = ['where', 'and'];

    protected function doLoadById(string $id)
    {
        return $this->ormManager->loadById($this, $id);
    }

    protected function doLoadAll()
    {
        return $this->ormManager->loadAll($this);
    }
}
