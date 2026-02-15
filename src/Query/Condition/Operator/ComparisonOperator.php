<?php

namespace Sarue\Orm\Query\Condition\Operator;

enum ComparisonOperator
{
    case GreaterThan;
    case GreaterThanOrEqualTo;
    case LessThan;
    case LessThanOrEqualTo;
    case EqualTo;
    case NotEqualTo;
}
