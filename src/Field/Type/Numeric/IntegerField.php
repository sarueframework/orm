<?php

namespace Sarue\Orm\Field\Type\Numeric;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class IntegerField extends NumericFieldBase
{
    const array ALLOWED_PROPERTY_TYPES = ['int'];
    const string COLUMN_TYPE = 'integer';
}
