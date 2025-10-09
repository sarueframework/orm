<?php

namespace Sarue\Orm\Field;

use Sarue\Orm\Exception\InvalidDefinitionException;
use Sarue\Orm\Exception\InvalidFieldClassException;

abstract class FieldBase implements FieldInterface
{
    public function __construct(
        protected string $fieldName,
        protected array $schemaDefinition = [],
        protected array $properties = [],
        protected bool $required = false,
    ) {}

    public static function getManagerClass(): string {
        return static::class . 'Manager';
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getSchemaDefinition(): array
    {
        return $this->schemaDefinition;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
