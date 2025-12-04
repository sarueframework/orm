<?php

namespace Sarue\Orm\Field\Type\Numeric;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class IntegerField extends NumericFieldBase
{
    public const array ALLOWED_PROPERTY_TYPES = ['int'];
    public const string COLUMN_TYPE = 'integer';
}
