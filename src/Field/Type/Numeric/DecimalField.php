<?php

namespace Sarue\Orm\Field\Type\Numeric;

use BcMath\Number;
use Doctrine\DBAL\Query\QueryBuilder;
use Sarue\Orm\Entity\EntityInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class DecimalField extends NumericFieldBase
{
    public const array ALLOWED_PROPERTY_TYPES = [Number::class];
    public const string COLUMN_TYPE = 'number';

    public function persistFieldToDatabase(QueryBuilder $queryBuilder, EntityInterface $entity): void
    {
        $queryBuilder->setValue($this->fieldName, $queryBuilder->createNamedParameter((string) $entity->{$this->fieldName}));
    }
}
