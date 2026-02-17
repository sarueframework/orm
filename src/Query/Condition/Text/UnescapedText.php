<?php

namespace Sarue\Orm\Query\Condition\Text;

final class UnescapedText implements \Stringable
{
    public function __construct(
        public readonly string $text,
    ) {
    }

    public function __toString(): string
    {
        return $this->text;
    }
}
