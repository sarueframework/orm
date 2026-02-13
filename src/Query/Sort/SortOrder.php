<?php

namespace Sarue\Orm\Query\Sort;

enum SortOrder: string
{
    case Asc = 'ASC';

    case Desc = 'DESC';

    case AscNullsFirst = 'ASC NULLS FIRST';

    case AscNullsLast = 'ASC NULLS LAST';

    case DescNullsFirst = 'DESC NULLS FIRST';

    case DescNullsLast = 'DESC NULLS LAST';
}
