<?php

namespace Sarue\Orm\Field;

interface FieldInterface
{
    public static function getConditionType(): string;
    public function getRawValue(): int|float|string|array;
    public function setRawValue(int|float|string|array $value): self;
}
