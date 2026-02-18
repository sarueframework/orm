<?php

namespace Sarue\Orm\Query\Parameter;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class AbstractParameter implements ParameterInterface
{
    protected const ParameterType|ArrayParameterType PARAMETER_TYPE = ParameterType::STRING;

    public readonly mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function toStringInQuery(QueryBuilder $queryBuilder): string
    {
        return $queryBuilder->createPositionalParameter($this->value, static::PARAMETER_TYPE);
    }
}
