<?php

namespace Sarue\Orm\Field\Type\DateTime;

use Sarue\Orm\Field\Type\AbstractScalarFieldType;
use Sarue\Orm\Query\Condition\DateTime\DateTimeConditionInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class DateField extends AbstractScalarFieldType
{
    // @todo Consider adding support to DateTimeImmutable
    public const array ALLOWED_PROPERTY_TYPES = ['DateTime'];

    public function fromDatabaseValue(mixed $databaseValue): string
    {
        var_dump($databaseValue);
        exit;
    }

    public function toDatabaseValues(mixed $fieldValue): array
    {
        if (is_null($fieldValue)) {
            if ($this->isRequired()) {
                throw new \Exception(sprintf('Value for field %s must not be null.', $this->getFieldName()));
            }
        } elseif (!$fieldValue instanceof \DateTime) {
            throw new \Exception(sprintf('Value for date field %s must be a \DateTime', $this->getFieldName()));
        }
    }

    public function getConditionType(): string
    {
        return DateTimeConditionInterface::class;
    }

    public function validateDefinition(): void
    {
    }

    protected function getColumnType(): string
    {
        return 'date';
    }
}
