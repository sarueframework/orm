<?php

namespace Sarue\Orm\Query;

class QueryFactoryBase
{
    public function __construct(
    ) {}

    public function instantiateQuery(string $class) {
        return new $class();
    }
}
