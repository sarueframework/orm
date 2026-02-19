<?php

namespace Sarue\Orm\Field\Data;

final class Metadata
{
    public function __construct(
        public readonly \DateTimeImmutable $created,
        public readonly \DateTimeImmutable $lastChange,
    ) {
    }
}
