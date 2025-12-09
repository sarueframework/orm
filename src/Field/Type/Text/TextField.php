<?php

namespace Sarue\Orm\Field\Type\Text;

use Sarue\Orm\Field\Type\ScalarFieldTypeBase;
use Sarue\Orm\Query\Condition\Text\TextConditionInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class TextField extends ScalarFieldTypeBase
{
    public const array ALLOWED_PROPERTY_TYPES = ['string'];
    public const string COLUMN_TYPE = 'string';

    public function getConditionType(): string
    {
        return TextConditionInterface::class;
    }

    public function validateDefinition(): void
    {
    }
}
