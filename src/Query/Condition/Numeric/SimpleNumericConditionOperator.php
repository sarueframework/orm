<?php

namespace Sarue\Orm\Query\Condition\Numeric;

enum SimpleNumericConditionOperator
{
    case GreaterThan;
    case GreaterThanOrEqualTo;
    case LessThan;
    case LessThanOrEqualTo;
    case Equal;
    case NotEqual;
}
