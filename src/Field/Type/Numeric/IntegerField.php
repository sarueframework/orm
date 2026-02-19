<?php

namespace Sarue\Orm\Field\Type\Numeric;

use Sarue\Orm\Query\Parameter\IntegerParameter;
use Sarue\Orm\Query\Parameter\NullParameter;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class IntegerField extends AbstractNumericField
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
        if (is_null($fieldValue)) {
            if ($this->isRequired()) {
                throw new \Exception(sprintf('Field %s must not be empty.', $this->fieldName));
            }

            $parameter = new NullParameter();
        } elseif (is_int($fieldValue)) {
            $parameter = new IntegerParameter($fieldValue);
        } else {
            throw new \Exception(sprintf('Field %s must be an int.', $this->fieldName));
        }

        return [$this->fieldName => $parameter];
    }
}
