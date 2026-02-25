<?php

namespace Sarue\Orm\Field\Type\DateTime;

use Sarue\Orm\Field\Type\AbstractScalarFieldType;
use Sarue\Orm\Query\Condition\DateTime\DateTimeConditionInterface;
use Sarue\Orm\Query\Parameter\NullParameter;
use Sarue\Orm\Query\Parameter\TextParameter;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class DateField extends AbstractScalarFieldType
{
    // @todo Consider adding support to DateTimeImmutable
    public const array ALLOWED_PROPERTY_TYPES = [\DateTime::class];

    public function fromDatabaseValue(mixed $databaseValue): ?\DateTime
    {
        if (is_null($databaseValue)) {
            return null;
        }

        if (!is_string($databaseValue)) {
            throw new \Exception('Database value must be a string');
        }

        return new \DateTime($databaseValue);
    }

    public function toDatabaseValues(mixed $fieldValue): array
    {
        if (is_null($fieldValue)) {
            if ($this->isRequired()) {
                throw new \Exception(sprintf('Value for field %s must not be null.', $this->getFieldName()));
            }

            $value = new NullParameter();
        } elseif (!$fieldValue instanceof \DateTime) {
            throw new \Exception(sprintf('Value for date field %s must be a \DateTime', $this->getFieldName()));
        } else {
            $value = new TextParameter($fieldValue->format('Y-m-d'));
        }

        return [
            $this->getFieldName() => $value,
        ];
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
