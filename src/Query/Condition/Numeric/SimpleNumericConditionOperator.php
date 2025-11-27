<?php

namespace Sarue\Orm\Query\Condition\Numeric;

enum SimpleNumericConditionOperator
{
    case GreaterThan;
    case GreaterThanOrEqualTo;
    case LessThan;
    case LessThanOrEqualTo;
    case EqualTo;
    case NotEqualTo;
}
