<?php

namespace Sarue\Orm\Tests\Integration\Helper;

use Sarue\Orm\Field\FieldBase;

class TestableField extends FieldBase
{
    public function __construct(
        string $fieldName,
        array $schemaDefinition,
        array $properties,
        bool $required,
        protected string $sqlColumnCode,
    ) {
        parent::__construct(
            $fieldName,
            $schemaDefinition,
            $properties,
            $required,
        );
    }

    public function sqlColumnCode(): string
    {
        return $this->sqlColumnCode;
    }
}
