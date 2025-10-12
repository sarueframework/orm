<?php

namespace Sarue\Orm\Query\Condition\Text;

class LikeCondition implements TextConditionInterface {
    public function __construct(
        public readonly string $sting,
    ) {}
}
