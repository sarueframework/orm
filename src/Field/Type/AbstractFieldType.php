<?php

namespace Sarue\Orm\Field\Type;

abstract class AbstractFieldType implements FieldTypeInterface
{
    public const array ALLOWED_PROPERTY_TYPES = [];

    /**
     * @var non-empty-string
     */
    final protected string $fieldName;

    final protected string $propertyType;

    final protected bool $required;

    final public function initializeDefinition(string $fieldName, string $propertyType): static
    {
        if (isset($this->fieldName) || isset($this->propertyType)) {
            throw new \Exception('Cannot override fieldName or propertyType.');
        }

        $this->setFieldName($fieldName);
        $this->setPropertyType($propertyType);

        return $this;
    }

    final public function getFieldName(): string
    {
        return $this->fieldName;
    }

    final public function getPropertyType(): string
    {
        return $this->propertyType;
    }

    final public function isRequired(): bool
    {
        return $this->required;
    }

    private function setFieldName(string $fieldName): void
    {
        if (empty($fieldName)) {
            throw new \Exception('Field name must not be empty.');
        }

        $this->fieldName = $fieldName;
    }

    private function setPropertyType(string $propertyType): void
    {
        if (empty($propertyType)) {
            throw new \Exception('Property type must not be empty.');
        }

        if (str_starts_with($propertyType, '?')) {
            $propertyType = substr($propertyType, 1);
            $this->required = false;
        }
        // The required may be set either here or by _set_state().
        elseif (!isset($this->required)) {
            $this->required = true;
        }

        if (str_contains($propertyType, '&') || str_contains($propertyType, '|')) {
            throw new \Exception('The use of composite types is not supported.');
        }

        if (empty(static::ALLOWED_PROPERTY_TYPES)) {
            throw new \Exception('The class '.get_class($this).' must set the constant ALLOWED_PROPERTY_TYPES.');
        }

        if (!in_array($propertyType, static::ALLOWED_PROPERTY_TYPES, strict: true)) {
            throw new \Exception('The property type for field '.$this->fieldName.' must be one of: "'.implode('", "', static::ALLOWED_PROPERTY_TYPES).'".');
        }

        $this->propertyType = $propertyType;
    }
}
