<?php

namespace Sarue\Orm\Query\Parameter;

use Doctrine\DBAL\Query\QueryBuilder;

interface ParameterInterface
{
    public function toStringInQuery(QueryBuilder $queryBuilder): string;
}
