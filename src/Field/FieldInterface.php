<?php

namespace Sarue\Orm\Field;

interface FieldInterface
{
    public static function getConditionType(): string;

    /**
     * @return \Doctrine\DBAL\Schema\Column[]
     *
     * @todo Pass a FieldDefinition object instead of string.
     */
    public static function getColumns(string $fieldName): array;

    public function getRawValue(): int|float|string|array;

    public function setRawValue(int|float|string|array $value): static;
}
