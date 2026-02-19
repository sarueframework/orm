<?php

namespace Sarue\Orm\Field\Type\Numeric;

use BcMath\Number;
use Doctrine\DBAL\Schema\ColumnEditor;
use Sarue\Orm\Query\Parameter\NullParameter;
use Sarue\Orm\Query\Parameter\NumberParameter;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class DecimalField extends AbstractNumericField
{
    public const int DEFAULT_PRECISION = 30;
    public const int DEFAULT_SCALE = 10;
    public const array ALLOWED_PROPERTY_TYPES = [Number::class];
    public const string COLUMN_TYPE = 'number';

    public function __construct(
        public readonly int $precision = self::DEFAULT_PRECISION,
        public readonly int $scale = self::DEFAULT_SCALE,
        int|Number|null $minimum = null,
        int|Number|null $maximum = null,
    ) {
        parent::__construct($minimum, $maximum);
    }

    public function fromDatabaseValue(mixed $databaseValue): Number
    {
        if (!is_numeric($databaseValue)) {
            throw new \Exception(sprintf('Value for %s must be numeric.', $this->fieldName));
        }

        if (is_float($databaseValue)) {
            throw new \Exception(sprintf('Value for %s must not be a float.', $this->fieldName));
        }

        return new Number($databaseValue);
    }

    public function toDatabaseValues(mixed $fieldValue): array
    {
        if (is_null($fieldValue)) {
            if ($this->isRequired()) {
                throw new \Exception(sprintf('Field %s must not be empty.', $this->fieldName));
            }

            $parameter = new NullParameter();
        } elseif ($fieldValue instanceof Number) {
            $parameter = new NumberParameter($fieldValue);
        } else {
            throw new \Exception(sprintf('Field %s must be a \\BcMath\\Number.', $this->fieldName));
        }

        return [$this->fieldName => $parameter];
    }

    protected function getColumnEditor(): ColumnEditor
    {
        return parent::getColumnEditor()
            ->setPrecision($this->precision)
            ->setScale($this->scale)
        ;
    }
}
