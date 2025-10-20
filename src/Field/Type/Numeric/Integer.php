<?php

namespace Sarue\Orm\Field\Type\Numeric;

use Doctrine\DBAL\Schema\Column;
use Sarue\Orm\Field\FieldTypeBase;
use Sarue\Orm\Query\Condition\Numeric\NumericConditionInterface;

class Integer extends FieldTypeBase
{
    protected int $value;

    public static function getConditionType(): string
    {
        return NumericConditionInterface::class;
    }

    public static function getColumns(string $fieldName): array
    {
        return [
            Column::editor()
                ->setUnquotedName($fieldName)
                ->setTypeName('integer')
                ->create(),
        ];
    }

    public function getRawValue(): int|float|string|array
    {
        return $this->get();
    }

    public function setRawValue(int|float|string|array $value): static
    {
        if (!is_int($value)) {
            throw new \Exception('Value "$value" is not an int');
        }

        return $this->set($value);
    }

    public function get(): int
    {
        return $this->value;
    }

    public function set(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getSchema(string $fieldName): Column
    {
        return Column::editor()
            ->setUnquotedName($fieldName)
            ->setTypeName('integer')
            ->setUnsigned(true)
            ->create();
    }
}
