<?php

namespace Sarue\Orm\Query\Condition\Group;

abstract class AbstractOrConditionGroup extends AbstractConditionGroup
{
    protected const array QUERY_METHODS = ['or'];

    protected const string CONJUNCTION = ' OR ';
}
