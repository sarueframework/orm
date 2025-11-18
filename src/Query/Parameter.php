<?php

namespace Sarue\Orm\Query;

final class Parameter
{
    public function __construct(
        public readonly int|float|string $value,
    ) {
    }
}
