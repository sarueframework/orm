<?php

namespace Sarue\Orm\Field\Type\Numeric;

use BcMath\Number;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class DecimalField extends NumericFieldBase
{
    const array ALLOWED_PROPERTY_TYPES = [Number::class];
    const string COLUMN_TYPE = 'number';
}
