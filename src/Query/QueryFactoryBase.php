<?php

namespace Sarue\Orm\Query;

class QueryFactoryBase
{
    public function __construct(
    ) {
    }

    protected function instantiateQuery(string $class): QueryInterface
    {
        if (!is_subclass_of($class, QueryInterface::class)) {
            throw new \Exception('Invalid query class.');
        }

        return new $class();
    }
}
