<?php

namespace Sarue\Orm\Query\Condition\Text;

use Sarue\Orm\Query\Condition\AbstractFieldCondition;

class LikeCondition extends AbstractFieldCondition implements TextConditionInterface
{
    public function __construct(
        public readonly string $sting,
    ) {
    }
}
