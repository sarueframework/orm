<?php

namespace Sarue\Orm\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Field
{
    public string $fieldName;

    /**
     * @var class-string
     */
    public string $fieldType;

    public function __construct()
    {
    }
}
