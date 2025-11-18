<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\OrmManager;

class QueryFactoryBase
{
    public function __construct(
        protected OrmManager $ormManager,
    ) {
    }

    protected function instantiateQuery(string $class): QueryInterface
    {
        if (!is_subclass_of($class, QueryInterface::class)) {
            throw new \Exception("Invalid query class '$class'.");
        }

        return new $class($this->ormManager);
    }
}
