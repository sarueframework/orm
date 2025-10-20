<?php

namespace Sarue\Orm\Field;

abstract class FieldTypeBase implements FieldTypeInterface
{
    public function __construct(
        protected string $fieldName,
        protected array $schemaDefinition = [],
        protected array $properties = [],
        protected bool $required = false,
    ) {
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
