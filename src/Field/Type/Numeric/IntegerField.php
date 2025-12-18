<?php

namespace Sarue\Orm\Field\Type\Numeric;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class IntegerField extends NumericFieldBase
{
    public const array ALLOWED_PROPERTY_TYPES = ['int'];
    public const string COLUMN_TYPE = 'integer';

    public function fromDatabaseValue(mixed $databaseValue): int
    {
        if (!is_int($databaseValue)) {
            throw new \Exception("{$this->fieldName} should be an integer.");
        }

        return $databaseValue;
    }
}
