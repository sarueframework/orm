<?php

namespace Sarue\Orm\Field\Type\Numeric;

use BcMath\Number;
use Doctrine\DBAL\Schema\Column;
use Sarue\Orm\Field\Type\ScalarFieldTypeBase;
use Sarue\Orm\Query\Condition\Numeric\NumericConditionInterface;

abstract class NumericFieldBase extends ScalarFieldTypeBase
{
    public function __construct(
        public readonly int|Number|null $minimum = null,
        public readonly int|Number|null $maximum = null,
    ) {
    }

    public function getConditionType(): string
    {
        return NumericConditionInterface::class;
    }

    public function getSchema(): array
    {
        return [
            Column::editor()
                ->setUnquotedName($this->fieldName)
                ->setTypeName('integer')
                ->setNotNull($this->required)
                ->create(),
        ];
    }

    public function validateDefinition(): void
    {
        if ($this->minimum > $this->maximum) {
            throw new \Exception('Maximum must be larger or equal than minimum');
        }
    }
}
