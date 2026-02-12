<?php

namespace Sarue\Orm\Query\Condition\Text;

use Sarue\Orm\Query\Condition\AbstractCondition;

class LikeCondition extends AbstractCondition implements TextConditionInterface
{
    public function __construct(
        public readonly string $sting,
    ) {
    }
}
