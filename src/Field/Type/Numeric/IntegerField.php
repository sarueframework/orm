<?php

namespace Sarue\Orm\Field\Type\Numeric;

use Doctrine\DBAL\Schema\Column;
use Exception;
use Sarue\Orm\Field\Type\FieldTypeBase;
use Sarue\Orm\Query\Condition\Numeric\NumericConditionInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class IntegerField extends FieldTypeBase
{
    const ALLOWED_PROPERTY_TYPES = ['int'];

    public function __construct(
        public readonly ?int $minimum = NULL,
        public readonly ?int $maximum = NULL,
    )
    {}

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
