<?php

namespace Sarue\Orm\Field\Type\Numeric;

use BcMath\Number;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\ColumnEditor;
use Sarue\Orm\Entity\EntityInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class DecimalField extends NumericFieldBase
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

    public function persistFieldToDatabase(QueryBuilder $queryBuilder, EntityInterface $entity): void
    {
        $queryBuilder->setValue($this->fieldName, $queryBuilder->createNamedParameter((string) $entity->{$this->fieldName}));
    }

    protected function getColumnEditor(): ColumnEditor
    {
        return parent::getColumnEditor()
            ->setPrecision($this->precision)
            ->setScale($this->scale)
        ;
    }
}
