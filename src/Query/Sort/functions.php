<?php

namespace Sarue\Orm\Query\Sort;

function asc(): ScalarSortExpression
{
    return new ScalarSortExpression(SortOrder::Asc);
}

function desc(): ScalarSortExpression
{
    return new ScalarSortExpression(SortOrder::Desc);
}

function ascNullsFirst(): ScalarSortExpression
{
    return new ScalarSortExpression(SortOrder::AscNullsFirst);
}

function descNullsLast(): ScalarSortExpression
{
    return new ScalarSortExpression(SortOrder::DescNullsLast);
}
