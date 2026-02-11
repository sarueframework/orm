<?php

namespace Sarue\Orm\Field\Type\Numeric;

use Sarue\Orm\Query\Parameter\IntegerParameter;
use Sarue\Orm\Query\Parameter\NullParameter;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class IntegerField extends NumericFieldBase
{
    public const array ALLOWED_PROPERTY_TYPES = ['int'];
    public const string COLUMN_TYPE = 'integer';

    public function fromDatabaseValue(mixed $databaseValue): ?int
    {
        if (is_null($databaseValue)) {
            return null;
        }

        if (!is_int($databaseValue)) {
            throw new \Exception("{$this->fieldName} should be an integer.");
        }

        return $databaseValue;
    }

    public function toDatabaseValues(mixed $fieldValue): array
    {
        // @todo Validate if value is string or ?string & not required.
        return [
            $this->fieldName => $fieldValue ? new IntegerParameter($fieldValue) : new NullParameter(),
        ];
    }
}
