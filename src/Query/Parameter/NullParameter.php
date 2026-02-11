<?php

namespace Sarue\Orm\Query\Parameter;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

class NullParameter implements ParameterInterface
{
    public function toStringInQuery(QueryBuilder $queryBuilder): string
    {
        return $queryBuilder->createPositionalParameter(null, ParameterType::NULL);
    }
}
