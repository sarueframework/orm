<?php

namespace Sarue\Orm\Field\Type\Numeric;

use Doctrine\DBAL\Schema\Column;
use Sarue\Orm\Field\FieldBase;
use Sarue\Orm\Query\Condition\Numeric\NumericConditionInterface;

class Integer extends FieldBase
{
    protected int $value;

    public static function getConditionType(): string {
        return NumericConditionInterface::class;
    }

    public function getRawValue(): int|float|string|array {
        return $this->get();
    }

    public function setRawValue(int|float|string|array $value): self {
        if (!is_int($value)) {
            throw new \Exception('Value "$value" is not an int');
        }

        return $this->set($value);
    }

    public function get(): int {
        return $this->value;
    }

    public function set(int $value): self {
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
