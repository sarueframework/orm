<?php

namespace Sarue\Orm\Field\FieldType\Text;

use Sarue\Orm\Field\FieldBase;

class StringFieldType extends FieldBase
{
    protected static function validateDefinition(array $rawDefinition, array $schema, array $properties, array $additionalDefinition, bool $required): void
    {
        // do nothing
    }
}
