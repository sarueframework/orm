<?php

namespace Sarue\Orm\Field\Type\Text;

use Doctrine\DBAL\Schema\Column;
use Sarue\Orm\Field\FieldBase;
use Sarue\Orm\Query\Condition\Text\TextConditionInterface;

class Text extends FieldBase
{
    protected int $value;

    public static function getConditionType(): string {
        return TextConditionInterface::class;
    }


    public function getRawValue(): int|float|string|array {
        return $this->get();
    }

    public function setRawValue(int|float|string|array $value): self {
        if (!is_string($value)) {
            throw new \Exception('Value "$value" is not a string');
        }

        return $this->set($value);
    }

    public function get(): string {
        return $this->value;
    }

    public function set(string $value): self {
        $this->value = $value;
        return $this;
    }

    public function getSchema(string $fieldName): Column
    {
        return Column::editor()
            ->setUnquotedName($fieldName)
            ->setTypeName('string')
            ->create();
    }
}
