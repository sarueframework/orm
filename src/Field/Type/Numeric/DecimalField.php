<?php

namespace Sarue\Orm\Field\Type\Numeric;

use BcMath\Number;
use Doctrine\DBAL\Schema\ColumnEditor;

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
        return parent::__construct($minimum, $maximum);
    }

    public function fromDatabaseValue(mixed $databaseValue): Number
    {
        return new Number($databaseValue);
    }

    protected function getColumnEditor(): ColumnEditor
    {
        return parent::getColumnEditor()
            ->setPrecision($this->precision)
            ->setScale($this->scale)
        ;
    }
}
