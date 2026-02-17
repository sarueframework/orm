<?php

namespace Sarue\Orm\Query\Condition\Text;

enum PatternMatchingOperator: string
{
    case Like = 'LIKE';

    case NotLike = 'NOT LIKE';

    case ILike = 'ILIKE';

    case NotILike = 'NOT ILIKE';

    case SimilarTo = 'SIMILAR TO';

    case NotSimilarTo = 'NOT SIMILAR TO';
}
