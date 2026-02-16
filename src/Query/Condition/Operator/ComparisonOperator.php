<?php

namespace Sarue\Orm\Query\Condition\Operator;

enum ComparisonOperator: string
{
    case GreaterThan = '>';
    case GreaterThanOrEqualTo = '>=';
    case LessThan = '<';
    case LessThanOrEqualTo = '<=';
    case EqualTo = '=';
    case NotEqualTo = '<>';
}
